<?php

namespace Polidog\ControllerFilterBundle\Tests\EventListener\Fixture;

use Polidog\ControllerFilterBundle\Annotations\Filter;

class FilterControllerAnnotationAtMethod
{
    /**
     * @Filter(Filter::TYPE_BEFORE, method="test", service="bar_service_name")
     */
    public function indexAction()
    {
    }

    /**
     * @Filter(Filter::TYPE_BEFORE, method="test", service="bar_service_name")
     * @Filter(Filter::TYPE_AFTER, method="test2", service="bar_service_name")
     * @Filter(Filter::TYPE_BEFORE, method="test3", service="bar_service_name")
     */
    public function multiAction()
    {
    }
}
