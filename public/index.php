<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // set methods PUT, PATCH (all methods different of GET|POST)
    Request::enableHttpMethodParameterOverride();
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
