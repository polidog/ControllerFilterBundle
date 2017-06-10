<?php

namespace Polidog\ControllerFilterBundle\Tests\Annotations;

use Polidog\ControllerFilterBundle\Annotations\Filter;
use Polidog\ControllerFilterBundle\Executor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class ExecutorTest extends KernelTestCase
{
    public function testRun()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $kernelEvent = $this->prophesize(KernelEvent::class);
        $container->get('executor_dummy')
            ->willReturn(new ExecutorDummy());

        $container->has('executor_dummy')
            ->willReturn(true);

        $e = new Executor($container->reveal());
        $filter = new Filter([
            'type' => Filter::TYPE_AFTER,
            'service' => 'executor_dummy',
            'method' => 'exec',
        ]);

        $result = $e->run($filter, $kernelEvent->reveal());
        $this->assertSame('call dummy exec method.', $result);

        $container->get('executor_dummy')->shouldHaveBeenCalled();
    }

}

class ExecutorDummy
{
    const MESSAGE = 'call dummy exec method.';

    public function exec()
    {
        return self::MESSAGE;
    }
}
