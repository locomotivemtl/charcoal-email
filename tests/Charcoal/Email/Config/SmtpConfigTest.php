<?php

namespace Charcoal\Tests\Email\Config;

use PHPUnit_Framework_TestCase;

use InvalidArgumentException;

use Charcoal\Email\Config\SmtpConfig;

class EmailConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SmtpConfig
     */
    public $obj;

    public function setUp()
    {
        $this->obj = new SmtpConfig();
    }

    public function testSetData()
    {
        $data = [
            'hostname' => 'example.com'
        ];
        $ret = $this->obj->merge($data);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('example.com', $this->obj->hostname());
    }

    public function testSetSmtpHostname()
    {
        $ret = $this->obj->setHostname('foobar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foobar', $this->obj->hostname());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setHostname([]);
    }

    public function testSetSmtpPort()
    {
        $ret = $this->obj->setPort(42);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(42, $this->obj->port());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setPort('foo');
    }

    public function testSetSmtpAuth()
    {
        $ret = $this->obj->setAuth(true);
        $this->assertSame($ret, $this->obj);
        $this->assertTrue($this->obj->auth());

        $this->obj->setAuth(false);
        $this->assertFalse($this->obj->auth());
    }

    public function testSetSmtpUsername()
    {
        $ret = $this->obj->setUsername('foobar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foobar', $this->obj->username());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setUsername([]);
    }

    public function testSetSmtpPassword()
    {
        $ret = $this->obj->setPassword('foobar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foobar', $this->obj->password());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setPassword([]);
    }

    public function testSetSecurity()
    {
        $ret = $this->obj->setSecurity('tls');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('TLS', $this->obj->security());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setSecurity('foobar');
    }
}
