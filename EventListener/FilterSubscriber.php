<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/12
 * Time: 19:20
 */

namespace Polidog\ControllerFilterBundle\EventListener;


use Polidog\ControllerFilterBundle\Annotations\Filter;
use Polidog\ControllerFilterBundle\Annotations\FilterInterface;
use Polidog\ControllerFilterBundle\Executor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class FilterSubscriber implements EventSubscriberInterface
{

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var object
     */
    private $controller;

    /**
     * FilterSubscriber constructor.
     * @param Executor $executor
     */
    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }


    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        if (!is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        } else if (!is_array($controller)) {
            return;
        }

        $classAnnotations = $request->attributes->get('_filter_class');
        $methodAnnotations = $request->attributes->get('_filter_method');
        $this->controller = $controller[0];

        if ($classAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_BEFORE, $classAnnotations, $this->controller, $event);
        }

        if ($methodAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_BEFORE, $methodAnnotations, $this->controller, $event);
        }

    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $classAnnotations = $request->attributes->get('_filter_class');
        $methodAnnotations = $request->attributes->get('_filter_method');

        if ($classAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_AFTER, $classAnnotations, $this->controller, $event);
        }
        if ($methodAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_AFTER, $methodAnnotations, $this->controller, $event);
        }

        return $event;

    }

    /**
     * @param string $type
     * @param array $annotations
     * @param object $controller controller object
     * @param KernelEvent $event
     */
    private function executeFilters(string $type, array $annotations, $controller, KernelEvent $event)
    {
        foreach ($annotations as $annotation) {
            $this->executeFilter($type, $annotation, $controller, $event);
        }
    }

    /**
     * @param string $type
     * @param FilterInterface $annotation
     * @param object $controller controller object
     * @param KernelEvent $event
     */
    private function executeFilter(string $type, FilterInterface $annotation, $controller, KernelEvent $event)
    {
        if ($annotation->getType() === $type) {
            $this->executor->run($annotation, $event);
        }
    }



    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController'],
            KernelEvents::VIEW => ['onKernelView']
        ];
    }


}
