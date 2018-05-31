<?php

namespace Charcoals\Tests\Email\Config;

use PHPUnit_Framework_TestCase;

use InvalidArgumentException;

use Charcoal\Email\Config\EmailConfig;

class EmailConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailConfig
     */
    public $obj;

    public function setUp()
    {
        $this->obj = new EmailConfig();
    }

    public function testSetData()
    {
        $data = [
            'defaultFrom'     => 'test@example.com'
        ];
        $ret = $this->obj->merge($data);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('test@example.com', $this->obj->defaultFrom());
    }


    public function testSetDefaultFrom()
    {
        $ret = $this->obj->setDefaultFrom('test@example.com');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('test@example.com', $this->obj->defaultFrom());
    }
}
