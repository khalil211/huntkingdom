<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\ProduitCommande;
use GestionProduitBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\User;

class MobileController extends Controller
{
    public function addToCartAction($idProduit, $idUser)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($idUser);
        $panier=$em->getRepository(Commande::class)->findOneBy(array('user'=>$user, 'etat'=>0));
        $produit=$em->getRepository(Produit::class)->find($idProduit);
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($panier);
        foreach ($pcs as $pc)
        {
            if ($pc->getProduit()->getId()==$idProduit)
            {
                $pc->setQuantite($pc->getQuantite()+1);
                $em->flush();
                return new Response('OK');
            }
        }
        $pc=new ProduitCommande();
        $pc->setQuantite(1);
        $pc->setCommande($panier);
        $pc->setProduit($produit);
        $em->persist($pc);
        $em->flush();
        return new Response('OK');
    }

    public function getPanierAction($idUser)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($idUser);
        $panier=$em->getRepository(Commande::class)->findOneBy(array('user'=>$user, 'etat'=>0));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($panier);
        return new JsonResponse($formatted);
    }

    public function getProduitsCommandeAction($idCommande)
    {
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->find($idCommande);
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($commande);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($pcs);
        return new JsonResponse($formatted);
    }

    public function modifierProduitAction($idProduit, $quantite)
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(ProduitCommande::class)->find($idProduit);
        $produit->setQuantite($quantite);
        $em->flush();
        return new Response('OK');
    }

    public function supprimerProduitAction($idProduit)
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(ProduitCommande::class)->find($idProduit);
        $em->remove($produit);
        $em->flush();
        return new Response('OK');
    }

    public function modifierCommandeAction($idCommande, $etat)
    {
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->find($idCommande);
        $commande->setEtat($etat);
        if ($etat==1)
        {
            $commande->setDate(new \DateTime());
            $panier=new Commande();
            $panier->setEtat(0);
            $panier->setUser($commande->getUser());
            $em->persist($panier);
        }
        $em->flush();
        return new Response('OK');
    }

    public function getCommandesAction($idUser)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($idUser);
        $commandes=$em->getRepository(Commande::class)->getUserCommandes($user);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($commandes);
        return new JsonResponse($formatted);
    }

    public function pdfAction($idCommande)
    {
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->find($idCommande);
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($commande);
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView('@Commande/back/facture.html.twig', array('commande'=>$commande, 'pcs'=>$pcs)),
            'factures/facture'.$idCommande.'.pdf',
            [],
            true
        );
        return new Response('OK');
    }

    public function verifierPanierAction($idUser)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($idUser);
        if (!$em->getRepository(Commande::class)->hasPanier($user))
        {
            $c=new Commande();
            $c->setEtat(0);
            $c->setUser($user);
            $em->persist($c);
            $em->flush();
        }
        return new Response('OK');
    }
}
