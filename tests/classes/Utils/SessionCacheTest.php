<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Utils;

use PhpMyAdmin\Config;
use PhpMyAdmin\Current;
use PhpMyAdmin\Utils\SessionCache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SessionCache::class)]
class SessionCacheTest extends TestCase
{
    public function testGet(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = null;

        SessionCache::set('test_data', 5);
        SessionCache::set('test_data_2', 5);

        $this->assertNotNull(SessionCache::get('test_data'));
        $this->assertNotNull(SessionCache::get('test_data_2'));
        $this->assertNull(SessionCache::get('fake_data_2'));
    }

    public function testRemove(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = null;
        Current::$server = 2;

        SessionCache::set('test_data', 25);
        SessionCache::set('test_data_2', 25);

        SessionCache::remove('test_data');
        $this->assertArrayNotHasKey('test_data', $_SESSION['cache']['server_2']);
        SessionCache::remove('test_data_2');
        $this->assertArrayNotHasKey('test_data_2', $_SESSION['cache']['server_2']);
    }

    public function testSet(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = null;
        Current::$server = 2;

        SessionCache::set('test_data', 25);
        SessionCache::set('test_data', 5);
        $this->assertEquals(5, $_SESSION['cache']['server_2']['test_data']);
        SessionCache::set('test_data_3', 3);
        $this->assertEquals(3, $_SESSION['cache']['server_2']['test_data_3']);
    }

    public function testHas(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = null;

        SessionCache::set('test_data', 5);
        SessionCache::set('test_data_2', 5);
        SessionCache::set('test_data_3', false);
        SessionCache::set('test_data_4', true);

        $this->assertTrue(SessionCache::has('test_data'));
        $this->assertTrue(SessionCache::has('test_data_2'));
        $this->assertTrue(SessionCache::has('test_data_3'));
        $this->assertTrue(SessionCache::has('test_data_4'));
        $this->assertFalse(SessionCache::has('fake_data_2'));
    }

    public function testKeyWithoutUser(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = null;
        Current::$server = 123;

        SessionCache::set('test_data', 5);
        $this->assertArrayHasKey('cache', $_SESSION);
        $this->assertIsArray($_SESSION['cache']);
        $this->assertArrayHasKey('server_123', $_SESSION['cache']);
        $this->assertIsArray($_SESSION['cache']['server_123']);
        $this->assertArrayHasKey('test_data', $_SESSION['cache']['server_123']);
        $this->assertSame(5, $_SESSION['cache']['server_123']['test_data']);
    }

    public function testKeyWithUser(): void
    {
        $_SESSION = [];
        Config::getInstance()->selectedServer['user'] = 'test_user';
        Current::$server = 123;

        SessionCache::set('test_data', 5);
        $this->assertArrayHasKey('cache', $_SESSION);
        $this->assertIsArray($_SESSION['cache']);
        $this->assertArrayHasKey('server_123_test_user', $_SESSION['cache']);
        $this->assertIsArray($_SESSION['cache']['server_123_test_user']);
        $this->assertArrayHasKey('test_data', $_SESSION['cache']['server_123_test_user']);
        $this->assertSame(5, $_SESSION['cache']['server_123_test_user']['test_data']);
    }
}
