<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/13
 * Time: 0:52
 */

namespace Polidog\ControllerFilterBundle;


use Polidog\ControllerFilterBundle\Annotations\FilterInterface;
use Polidog\ControllerFilterBundle\Exception\UnexpectedValueException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Executor
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Executor constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FilterInterface $filter
     * @param KernelEvent $event
     * @return mixed
     */
    public function run(FilterInterface $filter, KernelEvent $event)
    {
        if (false === $this->container->has($filter->getService())) {
            throw new UnexpectedValueException('Missing countainer id: '. $filter->getService());
        }

        return call_user_func_array([$this->container->get($filter->getService()), $filter->getMethod()],[$event]);
    }
}
