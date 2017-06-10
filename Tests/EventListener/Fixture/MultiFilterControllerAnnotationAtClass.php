<?php


namespace Polidog\ControllerFilterBundle\Tests\EventListener\Fixture;


use Polidog\ControllerFilterBundle\Annotations\Filter;

/**
 * @Filter(Filter::TYPE_BEFORE, method="foo", service="bar")
 * @Filter(Filter::TYPE_AFTER, method="foo2", service="bar")
 * @Filter(Filter::TYPE_AFTER, method="foo3", service="bar2")
 */
class MultiFilterControllerAnnotationAtClass
{
    public function indexAction()
    {

    }
}
