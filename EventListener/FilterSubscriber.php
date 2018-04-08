<?php
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
     * FilterSubscriber constructor.
     *
     * @param Executor $executor
     */
    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        $classAnnotations = $request->attributes->get('_filter_class');
        $methodAnnotations = $request->attributes->get('_filter_method');

        if ($classAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_BEFORE, $classAnnotations, $event);
        }

        if ($methodAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_BEFORE, $methodAnnotations, $event);
        }
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $classAnnotations = $request->attributes->get('_filter_class');
        $methodAnnotations = $request->attributes->get('_filter_method');

        if ($classAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_AFTER, $classAnnotations, $event);
        }
        if ($methodAnnotations !== null) {
            $this->executeFilters(Filter::TYPE_AFTER, $methodAnnotations, $event);
        }

        return $event;
    }

    /**
     * @param string      $type
     * @param array       $annotations
     * @param KernelEvent $event
     */
    private function executeFilters(string $type, array $annotations, KernelEvent $event)
    {
        foreach ($annotations as $annotation) {
            $this->executeFilter($type, $annotation, $event);
        }
    }

    /**
     * @param string          $type
     * @param FilterInterface $annotation
     * @param KernelEvent     $event
     */
    private function executeFilter(string $type, FilterInterface $annotation, KernelEvent $event)
    {
        if ($annotation->getType() === $type) {
            $this->executor->run($annotation, $event);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController'],
            KernelEvents::VIEW => ['onKernelView'],
        ];
    }
}
