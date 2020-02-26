<?php

namespace AnimalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AnimalBundle:Default:index.html.twig');
    }
}
