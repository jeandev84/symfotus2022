<?php
namespace App\DTO\Contract;

interface QueuingDTOInterface
{
    public function toAMQPMessage(): string;
}