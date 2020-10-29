<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Domain\PassportFacade;

class PassportController extends AbstractController
{
    /**
     * Returns JSON with passport data by series and number 
     * 
     * @param string $passportSeries
     * @param string $passportNumber
     * @return JsonResponse Passport entity
     */
    public function getPassportData(PassportFacade $passportFacade): JsonResponse 
    {
        $passportData = $passportFacade->doesPassportExist();
        return $passportData ? 
            $this->json('', 204) :
            $this->json('', 404);
    }
}
