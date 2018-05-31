<?php

namespace Charcoals\Tests\Email\Object;

use PHPUnit_Framework_TestCase;

use Charcoal\Email\Object\EmailQueueItem;

class EmailQueueItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailQueueItem
     */
    public $obj;

    public function setUp()
    {
        $container = $GLOBALS['container'];
        $this->obj = new EmailQueueItem([
            'logger' => $container['logger']
        ]);
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'ident' => 'phpunit'
        ]);
        $this->assertSame($this->obj, $ret);

        $this->assertEquals('phpunit', $this->obj->ident());
    }
}
