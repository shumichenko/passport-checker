<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function getStatusPage(): Response
    {
        return $this->render('default/status.html.twig', []);
    }
}
