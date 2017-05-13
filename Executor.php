<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/13
 * Time: 0:52
 */

namespace Polidog\ControllerFilterBundle;


use Polidog\ControllerFilterBundle\Annotations\FilterInterface;
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
     * @param FilterInterface $filter
     * @param Request $request
     * @return mixed
     */
    public function run(FilterInterface $filter, KernelEvent $event)
    {
        return call_user_func_array([$this->container->get($filter->getService()), $filter->getMethod()],[$event]);
    }
}
