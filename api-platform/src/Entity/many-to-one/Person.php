<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
class Person
{
    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(iris: 'http://schema.org/name')]
    public string $firstName;

    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(iris: 'http://schema.org/name')]
    public string $lastName;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Range(min: 0)]
    public int $age;
}
