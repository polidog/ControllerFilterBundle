<?php

namespace Polidog\ControllerFilterBundle\Tests\EventListener;

use Polidog\ControllerFilterBundle\Annotations\Filter;
use Polidog\ControllerFilterBundle\EventListener\FilterSubscriber;
use Polidog\ControllerFilterBundle\Executor;
use Polidog\ControllerFilterBundle\Tests\EventListener\Fixture\FilterSubscriberController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FilterSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterSubscriber
     */
    private $subsciber;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var FilterSubscriberController
     */
    private $controller;

    public function setUp()
    {
        $this->executor = $this->prophesize(Executor::class);
        $this->subsciber = new FilterSubscriber($this->executor->reveal());
        $this->request = new Request();
        $this->controller = new FilterSubscriberController();
    }

    /**
     * @dataProvider dataBeforeFilter
     *
     * @param Filter $filter
     * @param        $attrKey
     */
    public function testBeforeFilter(Filter $filter, $attrKey)
    {
        $this->request->attributes->set($attrKey, [
            $filter,
        ]);

        $event = $this->getFilterControllerEvent([$this->controller, 'indexAction'], $this->request);
        $this->subsciber->onKernelController($event);

        $this->executor->run($filter, $event)
            ->shouldHaveBeenCalled();
    }

    /**
     * @dataProvider dataAfterFilter
     *
     * @param Filter $filter
     * @param        $attrKey
     */
    public function testAfterFilter(Filter $filter, $attrKey)
    {
        $this->request->attributes->set($attrKey, [
            $filter,
        ]);

        $event = $this->getGetResponseForControllerResultEvent($this->request);
        $this->subsciber->onKernelView($event);

        $this->executor->run($filter, $event)
            ->shouldHaveBeenCalled();
    }

    public function dataBeforeFilter()
    {
        return [
            [
                new Filter([
                    'value' => Filter::TYPE_BEFORE,
                    'service' => 'foo',
                    'method' => 'barBeforeMethod',
                ]),
                '_filter_method',
            ],
            [
                new Filter([
                    'value' => Filter::TYPE_BEFORE,
                    'service' => 'foo',
                    'method' => 'barBeforeClass',
                ]),
                '_filter_class',
            ],
        ];
    }

    public function dataAfterFilter()
    {
        return [
            [
                new Filter([
                    'value' => Filter::TYPE_AFTER,
                    'service' => 'foo',
                    'method' => 'barAfterMethod',
                ]),
                '_filter_method',
            ],
            [
                new Filter([
                    'value' => Filter::TYPE_AFTER,
                    'service' => 'foo',
                    'method' => 'barAfterClass',
                ]),
                '_filter_class',
            ],
        ];
    }

    protected function getFilterControllerEvent($controller, Request $request)
    {
        $mockKernel = $this->getMockForAbstractClass('Symfony\Component\HttpKernel\Kernel', ['', '']);

        return new FilterControllerEvent($mockKernel, $controller, $request, HttpKernelInterface::MASTER_REQUEST);
    }

    protected function getGetResponseForControllerResultEvent(Request $request)
    {
        $mockEvent = $this->prophesize(GetResponseForControllerResultEvent::class);
        $mockEvent->getRequest()
            ->willReturn($request);

        return $mockEvent->reveal();
    }
}
