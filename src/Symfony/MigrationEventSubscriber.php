<?php
namespace App\Symfony;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;


/**
 * Resolve generate "public schema"
*/
class MigrationEventSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
         return ['postGenerateSchema'];
    }



    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
         $schema = $args->getSchema();

         if (! $schema->hasNamespace('public')) {
              $schema->createNamespace('public');
         }
    }
}