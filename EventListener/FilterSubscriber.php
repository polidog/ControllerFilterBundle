<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/12
 * Time: 19:20
 */

namespace Polidog\ControllerFilterBundle\EventListener;


use Doctrine\Common\Annotations\Reader;
use Polidog\ControllerFilterBundle\Annotations\Filter;
use Polidog\ControllerFilterBundle\Annotations\FilterInterface;
use Polidog\ControllerFilterBundle\Executor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Doctrine\Common\Util\ClassUtils;

class FilterSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $reader;

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
     * @param Reader $reader
     * @param Executor $executor
     */
    public function __construct(Reader $reader, Executor $executor)
    {
        $this->reader = $reader;
        $this->executor = $executor;
    }


    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();
        $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);

        $object = new \ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $classAnnotations = $this->getAnnotations($this->reader->getClassAnnotations($object));
        $methodAnnotations = $this->getAnnotations($this->reader->getMethodAnnotations($method));

        $this->executeFilters(Filter::TYPE_BEFORE, $classAnnotations, $controller[0], $event);
        $this->executeFilters(Filter::TYPE_BEFORE, $methodAnnotations, $controller[0], $event);

        $this->controller = $controller[0];

        $request->attributes->set('_filter_class', $classAnnotations);
        $request->attributes->set('_filter_method', $methodAnnotations);

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


    private function getAnnotations(array $annotations)
    {
        $configurations = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof FilterInterface) {
                $configurations[] = $annotation;
            }
        }
        return $configurations;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 10],
            KernelEvents::VIEW => ['onKernelView', 10]
        ];
    }


}
