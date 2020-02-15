<?php

namespace CommandeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function viewCartAction()
    {
        if ($this->getUser()==null)
            return $this->redirect('login');
        return $this->render('@Commande/Front/cart.html.twig');
    }
}
