<?php

namespace App\Entity\Contract;

interface HasMetaTimestampsInterface
{
    public function setCreatedAt();
    public function setUpdatedAt();
}