<?php
namespace App\Symfony\Convertor;

use App\Entity\Traits\SafeLoadFieldsTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationListInterface;


class MainParamConvertor implements ParamConverterInterface
{


    /**
     * @param ValidatorInterface $validator
    */
    public function __construct(protected ValidatorInterface $validator)
    {
    }


    /**
     * @param Request $httpRequest
     * @param ParamConverter $configuration
     * @return bool
     */
    public function apply(Request $httpRequest, ParamConverter $configuration): bool
    {
         // validationErrors ( это название параметра в saveUserAction() )
         $class = $configuration->getClass();
         /** @var SafeLoadFieldsTrait $request */
         $request = new $class();
         $request->loadFromJsonRequest($httpRequest);
         $errors = $this->validate($request, $httpRequest, $configuration);
         $httpRequest->attributes->set('validationErrors', $errors);

         return true;
    }


    /**
     * @param ParamConverter $configuration
     * @return bool
    */
    public function supports(ParamConverter $configuration)
    {
         return ! empty($configuration->getClass()) &&
                in_array(SafeLoadFieldsTrait::class, class_uses($configuration->getClass()), true);
    }


    /**
     * @param $request
     * @param Request $httpRequest
     * @param ParamConverter $configuration
     * @return ConstraintViolationListInterface
     */
    public function validate($request, Request $httpRequest, ParamConverter $configuration): ConstraintViolationListInterface
    {
         $httpRequest->attributes->set($configuration->getName(), $request);
         $options = (array)$configuration->getOptions();
         $resolver = new OptionsResolver();
         $resolver->setDefaults([
            'groups'   => null,
            'traverse' => false,
            'deep'     => false
         ]);

         $validatorOptions = $resolver->resolve($options['validator'] ?? []);

         return $this->validator->validate($request, null, $validatorOptions['groups']);
    }
}