<?php

namespace App;

use App\Symfony\Compiler\FormatterCompilerPass;
use App\Symfony\Compiler\GreeterCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;


    protected function build(ContainerBuilder $container)
    {
         $container->addCompilerPass(new FormatterCompilerPass());
         $container->addCompilerPass(new GreeterCompilerPass());
    }
}
