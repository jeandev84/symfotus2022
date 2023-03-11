<?php
namespace App\Controller\Api\SaveUser\v5\Output;

use App\Entity\Traits\SafeLoadFieldsTrait;
use JMS\Serializer\Annotation as JMS;


# класс представления, то что мы получаем на выход после сохранения пользователя
class UserIsSavedDTO
{
    use SafeLoadFieldsTrait;


    public int $id;

    public string $login;

    public int $age;


    #[JMS\SerializedName('isActive')]
    public bool $isActive;


    public function getSafeFields(): array
    {
        return ['id', 'login', 'age', 'isActive'];
    }
}