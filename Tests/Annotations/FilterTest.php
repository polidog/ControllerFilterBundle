<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/13
 * Time: 1:29
 */

namespace Polidog\ControllerFilterBundle\Tests\Annotations;


use Polidog\ControllerFilterBundle\Annotations\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testIsMethodFilter()
    {
        $filter = new Filter(['method' => 'test']);
        $this->assertTrue($filter->isMethodFilter());
        $this->assertFalse($filter->isServiceFilter());
    }

    public function testIsServiceFilter()
    {
        $filter = new Filter(['method' => 'test','service' => 'hoge']);
        $this->assertTrue($filter->isServiceFilter());
        $this->assertFalse($filter->isMethodFilter());
    }
}
