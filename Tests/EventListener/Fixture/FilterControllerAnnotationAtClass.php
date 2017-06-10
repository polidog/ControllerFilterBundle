<?php


namespace Polidog\ControllerFilterBundle\Tests\EventListener\Fixture;


use Polidog\ControllerFilterBundle\Annotations\Filter;

/**
 * @Filter(Filter::TYPE_BEFORE, method="foo", service="bar")
 */
class FilterControllerAnnotationAtClass
{
    public function indexAction()
    {

    }
}
