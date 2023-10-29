<?php
namespace App\DTO;

interface QueuingDTOInterface
{
    public function toAMQPMessage(): string;
}