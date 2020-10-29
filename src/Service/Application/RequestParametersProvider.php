<?php

namespace App\Service\Application;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestParametersProvider Returns required URL-parameters values
 * 
 * @package App\Api\Service\Application
 */
class RequestParametersProvider
{
    public static function getParameters(array $requiredParameters): array
    {
        $requestParameters = Request::createFromGlobals();
        $parametersValues = [];

        foreach ($requiredParameters as $parameter) {
            if (!is_null($requestParameters->get($parameter)))
                $parametersValues[$parameter] = $requestParameters->get($parameter);
        }

        return $parametersValues;
    }
}
