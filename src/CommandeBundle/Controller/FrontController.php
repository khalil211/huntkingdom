<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\ProduitCommande;
use GestionProduitBundle\Entity\Produit;
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
        $nb=0;
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
            $panier=$em->getRepository(Commande::class)->findBy(array('user' => $user, 'etat' => 0));
            $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($panier);
            foreach ($pcs as $pc)
            {
                $total+=$pc->getQuantite()*$pc->getProduit()->getPrixProd();
                $nb+=$pc->getQuantite();
            }
        }
        return $this->render('@Commande/front/miniCart.html.twig', array(
            'pcs'=>$pcs,
            'total'=>$total,
            'nb'=>$nb
        ));
    }

    public function addToCartAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        $panier=$em->getRepository(Commande::class)->findOneBy(array('user'=>$user, 'etat'=>0));
        $produit=$em->getRepository(Produit::class)->find($id);
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($panier);
        foreach ($pcs as $pc)
        {
            if ($pc->getProduit()->getId()==$id)
            {
                $pc->setQuantite($pc->getQuantite()+1);
                $em->flush();
                return $this->viewMiniCartAction();
            }
        }
        $pc=new ProduitCommande();
        $pc->setQuantite(1);
        $pc->setCommande($panier);
        $pc->setProduit($produit);
        $em->persist($pc);
        $em->flush();
        return $this->viewMiniCartAction();
    }
}
