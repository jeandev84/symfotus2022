<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Attributes\Extra;


#[ApiResource(normalizationContext: ['groups' => ['student:get']])]
#[ORM\Entity]
#[Extra(value: 'new student', number: 3)]
class Student extends Person
{
    #[ORM\Column(type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToMany(targetEntity: 'Teacher', inversedBy: 'students', fetch: 'LAZY')]
    #[ORM\JoinTable(name: 'student_teacher')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'teacher_id', referencedColumnName: 'id')]
    #[Groups(['student:get'])]
    public Collection $teachers;


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

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array
    {
        return $this->teachers->getValues();
    }

    public function addTeacher(Teacher $teacher): void
    {
        if ($this->teachers->contains($teacher)) {
            return;
        }
        $this->teachers->add($teacher);
        $teacher->addStudent($this);
    }

    public function removeTeacher(Teacher $teacher): void
    {
        if (!$this->teachers->contains($teacher)) {
            return;
        }
        $this->teachers->removeElement($teacher);
        $teacher->removeStudent($this);
    }



    #[Groups(['student:get'])]
    public function getAttribute(): string
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $extraAttributes = $reflectionClass->getAttributes(Extra::class);

        foreach ($extraAttributes as $attribute) {
            /** @var Extra $extra */
            $extra = $attribute->newInstance();

            return $extra->value.'$'.$extra->number;
        }
    }
}
