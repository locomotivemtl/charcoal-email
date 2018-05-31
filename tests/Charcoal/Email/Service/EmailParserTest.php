<?php

namespace Charcoal\Tests\Email\Service;

use PHPUnit_Framework_TestCase;

use Charcoal\Email\Service\EmailParser;
use InvalidArgumentException;

/**
 * Class EmailQueueManagerTest
 */
class EmailParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailParser
     */
    private $obj;

    public function setUp()
    {
        $this->obj = new EmailParser();
    }

    public function testEmailParseInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->parse(false);
    }

    /**
     * @dataProvider emailToArrayProvider
     */
    public function testToArray($val, $exp)
    {
        $res = $this->obj->toArray($val);
        $this->assertEquals($res, $exp);

        $this->expectException(InvalidArgumentException::class);
        $this->obj->toArray(false);
    }

    /**
     * @dataProvider emailFromArrayProvider
     */
    public function testFromArray($exp, $val)
    {
        $res = $this->obj->fromArray($val);
        $this->assertEquals($res, $exp);


        $this->expectException(InvalidArgumentException::class);
        $this->obj->fromArray(['name'=>'Yo']);
    }

    public function emailToArrayProvider()
    {
        return [
            [null, []],
            ['mat@locomotive.ca', ['email'=>'mat@locomotive.ca', 'name'=>'']],
            ['Mathieu <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu']],
            ["'Mathieu' <mat@locomotive.ca>", ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu']],
            ['"Mathieu Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu Mémo']],
            ['"M_athieu-Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'M_athieu-Mémo']],
            ['Alertes Mathieu-Loco <alertes@loco-motive_123.ca>', ['email'=>'alertes@loco-motive_123.ca', 'name'=>'Alertes Mathieu-Loco']],
            ['longtld@museum.com', ['email'=>'longtld@museum.com', 'name'=>'']],
            ['a.b-c-@d.e.f-g.com', ['email'=>'a.b-c-@d.e.f-g.com', 'name'=>'']]
        ];
    }

    public function emailFromArrayProvider()
    {
        return [
            ['mat@locomotive.ca', ['email'=>'mat@locomotive.ca', 'name'=>'']],
            ['"Mathieu" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu']],
            ['"Mathieu Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu Mémo']],
            ['"M_athieu-Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'M_athieu-Mémo']],
            ['"Alertes Mathieu-Loco" <alertes@loco-motive_123.ca>', ['email'=>'alertes@loco-motive_123.ca', 'name'=>'Alertes Mathieu-Loco']],
            ['longtld@museum.com', ['email'=>'longtld@museum.com', 'name'=>'']],
            ['"Test" <a.b-c-@d.e.f-g.com>', ['email'=>'a.b-c-@d.e.f-g.com', 'name'=>'Test']]
        ];
    }
}
