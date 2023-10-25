<?php
namespace App\Controller\Common;

class ErrorResponse
{

    /** @var bool */
    public bool $success = false;

    /** @var Error[] */
    public array $errors;


    /**
     * @param Error ...$errors
    */
    public function __construct(Error ...$errors)
    {
        $this->errors = $errors;
    }
}