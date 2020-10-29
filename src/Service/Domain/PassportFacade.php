<?php

namespace App\Service\Domain;

use App\Service\Application\RequestParametersProvider;

class PassportFacade
{
    private $passportFetcher;

    public function __construct(PassportFetcher $passportFetcher)
    {
        $this->passportFetcher = $passportFetcher;
    }

    public function doesPassportExist(): bool
    {
        $requestParameters = RequestParametersProvider::getParameters(['passportSeries', 'passportNumber']);
        $passportSeries = $requestParameters['passportSeries'] ?? '';
        $passportNumber = $requestParameters['passportNumber'] ?? '';
        
        return $doesPassportExist = $this->passportFetcher->fetchPassport($passportSeries, $passportNumber);
    }
}