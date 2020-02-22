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
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $total=0;
        $em=$this->getDoctrine()->getManager();
        $panier=$em->getRepository(Commande::class)->findBy(array('user' => $user, 'etat' => 0));
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($panier);
        foreach ($pcs as $pc)
            $total+=$pc->getQuantite()*$pc->getProduit()->getPrixProd();
        return $this->render('@Commande/Front/cart.html.twig', array(
            'total'=>$total,
            'pcs'=>$pcs
        ));
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

    public function deleteFromCartAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($em->getRepository(ProduitCommande::class)->find($id));
        $em->flush();
        return $this->viewMiniCartAction();
    }

    public function passerCommandeAction()
    {
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->findOneBy(array('user'=>$user, 'etat'=>0));
        $commande->setEtat(1);
        $commande->setDate(new \DateTime());
        $panier=new Commande();
        $panier->setEtat(0);
        $panier->setUser($user);
        $em->persist($panier);
        $em->flush();
        return $this->redirectToRoute('view_cart');
    }

    public function editCartAction($id,$nb)
    {
        $em=$this->getDoctrine()->getManager();
        $pc=$em->getRepository(ProduitCommande::class)->find($id);
        if(($pc->getQuantite()+$nb)>=1)
            $pc->setQuantite($pc->getQuantite()+$nb);
        $em->flush();
        return $this->viewMiniCartAction();
    }

    public function mesCommandesAction()
    {
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $em=$this->getDoctrine()->getManager();
        $commandes=$em->getRepository(Commande::class)->getUserCommandes($user);
        $pcs=Array();
        foreach ($commandes as $commande)
            $pcs[$commande->getId()]=$this->getDoctrine()->getRepository(ProduitCommande::class)->findByCommande($commande);
        return $this->render('@Commande/front/commandes.html.twig', array('commandes'=>$commandes, 'pcs'=>$pcs));
    }

    public function detailsCommandeAction($id)
    {
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $commande=$this->getDoctrine()->getRepository(Commande::class)->find($id);
        $pcs=$this->getDoctrine()->getRepository(ProduitCommande::class)->findByCommande($commande);
        return $this->render('@Commande/front/detailsCommande.html.twig', array(
            'commande'=>$commande,
            'pcs'=>$pcs
        ));
    }

    public function annulerCommandeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->find($id);
        $commande->setEtat(3);
        $em->flush();
        return $this->redirectToRoute('consulter_commande', array('id'=>$id));
    }
}
