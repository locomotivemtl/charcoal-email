<?php

namespace Charcoals\Tests\Email\Service;

use PHPUnit_Framework_TestCase;

use Charcoal\Email\Object\EmailQueueItem;
use Charcoal\Email\Service\EmailQueueManager;

/**
 * Class EmailQueueManagerTest
 */
class EmailQueueManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailQueueManager
     */
    public $obj;

    public function setUp()
    {
        $container = $GLOBALS['container'];
        $this->obj = new EmailQueueManager([
            'logger' => $container['logger'],
            'queue_item_factory' => $container['model/factory']
        ]);
    }

    public function testProto()
    {
        $ret = $this->obj->queueItemProto();
        $this->assertInstanceOf(EmailQueueItem::class, $ret);
    }
}
