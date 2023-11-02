<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['teacher:get']])]
#[ORM\Entity]
class Teacher extends Person
{
    #[ORM\Column(type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToMany(targetEntity: 'Student', inversedBy: 'teachers', fetch: 'LAZY')]
    #[Groups(['teacher:get'])]
    public Collection $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    #[Groups(['teacher:get'])]
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    #[Groups(['teacher:get'])]
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array
    {
        return $this->students->getValues();
    }

    public function addStudent(Student $student): void
    {
        if ($this->students->contains($student)) {
            return;
        }
        $this->students->add($student);
        $student->addTeacher($this);
    }

    public function removeStudent(Student $student): void
    {
        if (!$this->students->contains($student)) {
            return;
        }
        $this->students->removeElement($student);
        $student->removeTeacher($this);
    }
}
