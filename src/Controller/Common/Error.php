<?php
namespace App\Controller\Common;


class Error
{

     /**
      * @var string
     */
     public string $propertyPath;


     /**
      * @var string
     */
     public string $message;



     /**
      * @param string $propertyPath
      * @param string $message
     */
     public function __construct(string $propertyPath, string $message)
     {
          $this->propertyPath = $propertyPath;
          $this->message      = $message;
     }
}