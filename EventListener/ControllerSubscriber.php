<?php

namespace Polidog\ControllerFilterBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Polidog\ControllerFilterBundle\Annotations\FilterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Common\Util\ClassUtils;

class ControllerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();
        $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);

        if (!is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        } elseif (!is_array($controller)) {
            return;
        }

        $object = new \ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $classAnnotations = $this->getAnnotations($this->annotationReader->getClassAnnotations($object));
        $methodAnnotations = $this->getAnnotations($this->annotationReader->getMethodAnnotations($method));

        $request->attributes->set('_filter_class', $classAnnotations);
        $request->attributes->set('_filter_method', $methodAnnotations);
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
            KernelEvents::CONTROLLER => ['onKernelController'],
        ];
    }
}
