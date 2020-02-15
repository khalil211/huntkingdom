<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\ProduitCommande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function viewCartAction()
    {
        if ($this->getUser()==null)
            return $this->redirect('login');
        return $this->render('@Commande/Front/cart.html.twig');
    }

    public function viewMiniCartAction()
    {
        $total=0;
        $pcs=Array();
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        if ($user!=null)
        {
            if (!$em->getRepository(Commande::class)->hasPanier($user))
            {
                $commande=new Commande();
                $commande->setUser($user);
                $commande->setEtat(0);
                $em->persist($commande);
                $em->flush();
            }
            $panier=$em->getRepository(Commande::class)->findBy(array('user'=>$user, 'etat'=>0));
            $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($panier);
            foreach ($pcs as $pc)
                $total+=$pc->getQuantite*$pc->getProduit()->getPrixProd();

        }
        return $this->render('@Commande/front/miniCart.html.twig', array(
            'pcs'=>$pcs,
            'total'=>$total
        ));
    }

    public function addToCartAction()
    {
        
    }
}
