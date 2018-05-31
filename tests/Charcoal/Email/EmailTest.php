<?php

namespace Charcoal\Tests\App\Email;

use PHPUnit_Framework_TestCase;

use InvalidArgumentException;

use Charcoal\Email\Email;

/**
 * Test the AbstractEmail methods, through concrete `Email` class.
 */
class EmailTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Email
     */
    public $obj;

    public function setup()
    {
        /** GLOBALS['container'] is defined in bootstrap file */
        $container = $GLOBALS['container'];
        $this->obj = new Email([
            'logger'    => $container['logger'],
            'config'    => $container['email/config'],
            'view'      => $container['email/view'],
            'templateFactory' => $container['template/factory'],
            'parser'    => $container['email/parser'],
            'sender'    => $container['email/sender'],
            'tracker'   => $container['email/tracker']
        ]);
    }

    public function testSetData()
    {
        $obj = $this->obj;
        $ret = $obj->setData([
            'campaign'      => 'foo',
            'to'            => 'test@example.com',
            'cc'            => 'cc@example.com',
            'bcc'           => 'bcc@example.com',
            'from'          => 'from@example.com',
            'replyTo'       => 'reply@example.com',
            'subject'       => 'bar',
            'messageHtml'       => 'foo',
            'messageTxt'        => 'baz',
            'attachments'   => [
                'foo'
            ],
            'templateIdent' => 'foobar',
            'templateData'  => [
                'foo' => 'bar'
            ],
            'logEnabled'   => true,
            'trackEnabled' => true
        ]);
        $this->assertSame($ret, $obj);

        $this->assertEquals('foo', $obj->campaign());
        $this->assertEquals(['test@example.com'], $obj->to());
        $this->assertEquals(['cc@example.com'], $obj->cc());
        $this->assertEquals(['bcc@example.com'], $obj->bcc());
        $this->assertEquals('from@example.com', $obj->from());
        $this->assertEquals('reply@example.com', $obj->replyTo());
        $this->assertEquals('bar', $obj->subject());
        $this->assertEquals('foo', $obj->messageHtml());
        $this->assertEquals('baz', $obj->messageTxt());
        $this->assertEquals(['foo'], $obj->attachments());
        $this->assertEquals('foobar', $obj->templateIdent());
        $this->assertEquals(['foo'=>'bar'], $obj->templateData());
        $this->assertEquals(true, $obj->logEnabled());
        $this->assertEquals(true, $obj->trackEnabled());
    }

    public function testSetCampaign()
    {
        $obj = $this->obj;
        $ret = $obj->setCampaign('foo');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foo', $obj->campaign());

        $this->expectException(InvalidArgumentException::class);
        $obj->setCampaign([1, 2, 3]);
    }

    public function testGenerateCampaign()
    {
        $obj = $this->obj;
        $ret = $obj->campaign();
        $this->assertNotEmpty($ret);
    }

    /**
     * Asserts that the `setTo()` method:
     * - Sets the "to" recipient when using an array of string
     * - Sets the "to" recipient properly when using an email structure (array)
     * - Sets the "to" recipient to an array when setting a single email string
     * - Resets the "to" value before setting it, at every call.
     * - Throws an exception if the to argument is not a string.
     */
    public function testSetTo()
    {
        $obj = $this->obj;

        $ret = $obj->setTo([
            'test@example.com',
            'test2@example.com']);
        $this->assertSame($ret, $obj);
        $this->assertEquals([
            'test@example.com',
            'test2@example.com'
        ], $obj->to());

        $obj->setTo([
            [
                'name'  => 'Test',
                'email' => 'test@example.com'
            ]
        ]);
        $this->assertEquals(['"Test" <test@example.com>'], $obj->to());

        $obj->setTo('test@example.com');
        $this->assertEquals(['test@example.com'], $obj->to());

        $this->expectException(InvalidArgumentException::class);
        $obj->setTo(false);

        $this->expectException(InvalidArgumentException::class);
        $obj->setTo(false);
    }

    public function testAddTo()
    {
        $obj = $this->obj;
        $ret = $obj->addTo('test@example.com');
        $this->assertSame($ret, $obj);
        $this->assertEquals(['test@example.com'], $obj->to());

        $obj->addTo(['name'=>'Test','email'=>'test@example.com']);
        $this->assertEquals(['test@example.com', '"Test" <test@example.com>'], $obj->to());

        $this->expectException(InvalidArgumentException::class);
        $obj->addTo(false);
    }

    public function testSetCc()
    {
        $obj = $this->obj;

        $ret = $obj->setCc(['test@example.com']);
        $this->assertSame($ret, $obj);
        $this->assertEquals(['test@example.com'], $obj->cc());

        $obj->setCc([
            [
                'name'  => 'Test',
                'email' => 'test@example.com'
            ]
        ]);
        $this->assertEquals(['"Test" <test@example.com>'], $obj->cc());

        $obj->setCc('test@example.com');
        $this->assertEquals(['test@example.com'], $obj->cc());

        $this->expectException(InvalidArgumentException::class);
        $obj->SetCc(false);
    }

    public function testAddCc()
    {
        $obj = $this->obj;
        $ret = $obj->addCc('test@example.com');
        $this->assertSame($ret, $obj);
        $this->assertEquals(['test@example.com'], $obj->cc());

        $obj->addCc(['name'=>'Test','email'=>'test@example.com']);
        $this->assertEquals(['test@example.com', '"Test" <test@example.com>'], $obj->cc());

        $this->expectException(InvalidArgumentException::class);
        $obj->addCc(false);
    }

    public function testSetBcc()
    {
        $obj = $this->obj;

        $ret = $obj->setBcc(['test@example.com']);
        $this->assertSame($ret, $obj);
        $this->assertEquals(['test@example.com'], $obj->bcc());

        $obj->setBcc([
            [
                'name'  => 'Test',
                'email' => 'test@example.com'
            ]
        ]);
        $this->assertEquals(['"Test" <test@example.com>'], $obj->bcc());

        $obj->setBcc('test@example.com');
        $this->assertEquals(['test@example.com'], $obj->bcc());

        $this->expectException(InvalidArgumentException::class);
        $obj->setBcc(false);
    }

    public function testAddBcc()
    {
        $obj = $this->obj;
        $ret = $obj->addBcc('test@example.com');
        $this->assertSame($ret, $obj);
        $this->assertEquals(['test@example.com'], $obj->bcc());

        $obj->addBcc(['name'=>'Test','email'=>'test@example.com']);
        $this->assertEquals(['test@example.com', '"Test" <test@example.com>'], $obj->bcc());

        $this->expectException(InvalidArgumentException::class);
        $obj->addBcc(false);
    }

    public function testSetFrom()
    {
        $obj = $this->obj;
        //$config = $obj->config()->setDefaultFrom('default@example.com');
        //$this->assertEquals('default@example.com', $obj->from());

        $ret = $obj->setFrom('test@example.com');
        $this->assertSame($ret, $obj);
        $this->assertEquals('test@example.com', $obj->from());

        $obj->setFrom([
            'name'  => 'Test',
            'email' => 'test@example.com'
        ]);
        $this->assertEquals('"Test" <test@example.com>', $obj->from());

        $this->expectException(InvalidArgumentException::class);
        $obj->setFrom(false);
    }

    public function testSetReplyTo()
    {
        $obj = $this->obj;
        //$config = $obj->config()->setDefaultReplyTo('default@example.com');
        //$this->assertEquals('default@example.com', $obj->replyTo());

        $ret = $obj->setReplyTo('test@example.com');
        $this->assertSame($ret, $obj);
        $this->assertEquals('test@example.com', $obj->replyTo());

        $obj->setReplyTo([
            'name'  => 'Test',
            'email' => 'test@example.com'
        ]);
        $this->assertEquals('"Test" <test@example.com>', $obj->replyTo());

        $this->expectException(InvalidArgumentException::class);
        $obj->setReplyTo(false);
    }

    public function testSetSubject()
    {
        $obj = $this->obj;
        $ret = $obj->setSubject('foo');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foo', $obj->subject());

        $this->expectException(InvalidArgumentException::class);
        $obj->setSubject(null);
    }

    public function testSetMessageHtml()
    {
        $obj = $this->obj;
        $ret = $obj->setMessageHtml('foo');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foo', $obj->messageHtml());

        $this->expectException(InvalidArgumentException::class);
        $obj->setMessageHtml(null);
    }

    public function testSetMessageTxt()
    {
        $obj = $this->obj;
        $ret = $obj->setMessageTxt('foo');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foo', $obj->messageTxt());

        $this->expectException(InvalidArgumentException::class);
        $obj->setMessageTxt(null);
    }

    public function testConvertHtml()
    {
        $obj = $this->obj;
        $html = file_get_contents(__DIR__.'/../../data/example.html');
        $txt = file_get_contents(__DIR__.'/../../data/example.txt');

        $obj->setMessageHtml($html);

        // Next assert add a "\n" because the txt file ends with a newline,
        $this->assertEquals($txt, $obj->messageTxt());
    }

    public function testSetAttachments()
    {
        $obj = $this->obj;
        $ret = $obj->setAttachments(['foo']);
        $this->assertSame($ret, $obj);
        $this->assertEquals(['foo'], $obj->attachments());
    }

    public function testSetLogEnabled()
    {
        $obj = $this->obj;
        // $this->config()->setDefaultLog(false);
        // $this->assertNotTrue($obj->log());

        $ret = $this->obj->setLogEnabled(true);
        $this->assertSame($ret, $this->obj);
        $this->assertTrue($this->obj->logEnabled());

        $obj->setLogEnabled(false);
        $this->assertFalse($this->obj->logEnabled());
    }

    public function testSetTrackEnabled()
    {
        $obj = $this->obj;
        // $this->config()->setDefaultTrackEnabled(false);
        // $this->assertNotTrue($obj->trackEnabled());

        $ret = $obj->setTrackEnabled(true);
        $this->assertSame($ret, $obj);
        $this->assertTrue($obj->trackEnabled());

        $obj->setTrackEnabled(false);
        $this->assertNotTrue($obj->trackEnabled());
    }
}
