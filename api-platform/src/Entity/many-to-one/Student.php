<?php
namespace App\Entity;

#use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['student:get']])]
#[ORM\Entity]
class Student extends Person
{
    #[ORM\Column(type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'Teacher', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: 'true')]
    public ?Teacher $teacher;

    public function getId(): int
    {
        return (int)$this->id;
    }



    #[Groups(['student:get'])]
    public function getFirstName(): string
    {
        return $this->firstName;
    }



    #[Groups(['student:get'])]
    public function getLastName(): string
    {
        return $this->lastName;
    }



    #[Groups(['student:get'])]
    public function getAge(): int
    {
        return $this->age;
    }


    #[ApiProperty(iris: 'http://schema.org/name')]
    public function getName(): string
    {
        return $this->firstName.' '. $this->lastName;
    }
}
