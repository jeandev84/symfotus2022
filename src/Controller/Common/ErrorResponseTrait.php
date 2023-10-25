<?php
namespace App\Controller\Common;

use FOS\RestBundle\View\View;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;


trait ErrorResponseTrait
{
     private function createValidationErrorResponse(int $code, ConstraintViolationListInterface $violationErrors)
     {
          $errors = [];

          foreach ($violationErrors as $error) {

              /** @var ConstraintViolationInterface $error */
              $errors[] = new Error($error->getPropertyPath(), $error->getMessage());
          }

          return View::create(new ErrorResponse(...$errors), $code);
     }
}