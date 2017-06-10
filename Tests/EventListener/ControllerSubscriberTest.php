<?php

namespace Polidog\ControllerFilterBundle\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Polidog\ControllerFilterBundle\Annotations\Filter;
use Polidog\ControllerFilterBundle\EventListener\ControllerSubscriber;
use Polidog\ControllerFilterBundle\Tests\EventListener\Fixture\FilterControllerAnnotationAtClass;
use Polidog\ControllerFilterBundle\Tests\EventListener\Fixture\FilterControllerAnnotationAtMethod;
use Polidog\ControllerFilterBundle\Tests\EventListener\Fixture\MultiFilterControllerAnnotationAtClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ControllerSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ControllerSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->request = new Request();
        $this->subscriber = new ControllerSubscriber(new AnnotationReader());
    }

    public function testFilterAnnotationAtMethod()
    {
        $controller = new FilterControllerAnnotationAtMethod();
        $event = $this->getFilterControllerEvent([$controller, 'indexAction'], $this->request);
        $this->subscriber->onKernelController($event);

        $annotations = $this->request->get('_filter_method');
        $this->assertCount(1, $annotations);
        $this->assertInstanceOf(Filter::class, $annotations[0]);
    }

    public function testMultiFilterAnnotationAtMethod()
    {
        $controller = new FilterControllerAnnotationAtMethod();
        $event = $this->getFilterControllerEvent([$controller, 'multiAction'], $this->request);
        $this->subscriber->onKernelController($event);

        $annotations = $this->request->get('_filter_method');
        $this->assertCount(3, $annotations);

        foreach ($annotations as $annotation) {
            $this->assertInstanceOf(Filter::class, $annotation);
        }
    }

    public function testFilterAnnotationAtClass()
    {
        $controller = new FilterControllerAnnotationAtClass();
        $event = $this->getFilterControllerEvent([$controller, 'indexAction'], $this->request);
        $this->subscriber->onKernelController($event);

        $annotations = $this->request->get('_filter_class');
        $this->assertCount(1, $annotations);
        $this->assertInstanceOf(Filter::class, $annotations[0]);
    }

    public function testMultiFilterAnnotationAtClass()
    {
        $controller = new MultiFilterControllerAnnotationAtClass();
        $event = $this->getFilterControllerEvent([$controller, 'indexAction'], $this->request);
        $this->subscriber->onKernelController($event);

        $annotations = $this->request->get('_filter_class');
        $this->assertCount(3, $annotations);

        foreach ($annotations as $annotation) {
            $this->assertInstanceOf(Filter::class, $annotation);
        }
    }

    protected function getFilterControllerEvent($controller, Request $request)
    {
        $mockKernel = $this->getMockForAbstractClass('Symfony\Component\HttpKernel\Kernel', ['', '']);

        return new FilterControllerEvent($mockKernel, $controller, $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
