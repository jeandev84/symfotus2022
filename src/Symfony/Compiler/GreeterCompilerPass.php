<?php
namespace App\Symfony\Compiler;

use App\Service\MessageService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


// AUTO INJECT greeter services
class GreeterCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
         if (! $container->has(MessageService::class)) {
              return;
         }

         $messageService  = $container->findDefinition(MessageService::class);
         $greeterServices = $container->findTaggedServiceIds('app.greeter_service');

         // sort by priority
         uasort($greeterServices, static fn(array $tag1, array $tag2) => $tag1[0]['priority'] - $tag2[0]['priority']);

         /* dd($greeterServices); */
         foreach ($greeterServices as $id => $tags) {
             $messageService->addMethodCall('addGreeter', [new Reference($id)]);
         }

         /* dd($messageService); */
         // это тоже самое писать в разделе "services" внутри файла./config/services.yaml
         // services:
         //   ...
         //   MessageService:
         //     calls:
         //       -[addGreeter, ['@cite_formatter']]
    }
}