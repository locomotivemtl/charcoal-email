<?php

namespace Charcoal\Tests\Email\Service;

use PHPUnit_Framework_TestCase;

use Charcoal\Email\Service\EmailSender;
use InvalidArgumentException;

/**
 * Class EmailQueueManagerTest
 */
class EmailSenderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailParser
     */
    private $obj;

    public function setUp()
    {
        $container = $GLOBALS['container'];
        $this->obj = new EmailSender([
            'logger' => $container['logger'],
            'smtpConfig' => $container['email/config/smtp'],
            'queueItemFactory' => $container['model/factory'],
            'parser' => $container['email/parser'],
            'tracker' => $container['email/tracker'],
            'defaultFrom' => 'charcoal@example.com'
        ]);
    }

    public function testSend()
    {
        $email = $GLOBALS['container']['email'];
        $email->setTo('phpunit@example.com');
        $ret = $this->obj->send($email);
        $this->assertFalse($ret);
    }

    public function testQueue()
    {
        $email = $GLOBALS['container']['email'];
        $email->setTo('phpunit@example.com');
        $email->setSubject('Allo');
        $ret = $this->obj->queue($email, 'now');
        $this->assertTrue($ret);
    }
}
