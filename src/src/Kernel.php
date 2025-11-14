<?php

namespace App;

use App\Module\Note\Infrastructure\DI\LoggableEventsPass;
use App\Module\Note\Infrastructure\DI\NotifiableEventsPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new LoggableEventsPass());
        $container->addCompilerPass(new NotifiableEventsPass());
    }
}
