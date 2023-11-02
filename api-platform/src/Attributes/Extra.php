<?php
namespace App\Attributes;

use Attribute;
use Symfony\Contracts\Service\Attribute\Required;

#[Attribute]
class Extra
{
    #[Required]
    public string $value;

    public int $number;

    public function __construct(string $value, int $number)
    {
        $this->value = $value;
        $this->number = $number;
    }
}
