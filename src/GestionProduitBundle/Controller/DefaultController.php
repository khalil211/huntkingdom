<?php

namespace GestionProduitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GestionProduitBundle:Default:index.html.twig');
    }
}
