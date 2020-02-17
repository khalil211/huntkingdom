<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\ProduitCommande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    public function listCommandeAction()
    {
        $commandes=$this->getDoctrine()->getRepository(Commande::class)->getCommandes();
        $pcs=Array();
        foreach ($commandes as $commande)
        {
            $pcs[$commande->getId()]=$this->getDoctrine()->getRepository(ProduitCommande::class)->findByCommande($commande);
        }
        return $this->render('@Commande/back/list.html.twig', array('commandes'=>$commandes, 'pcs'=>$pcs));
    }

    public function editCommandeAction($id, $etat)
    {
        if ($etat==2 || $etat==3)
        {
            $em=$this->getDoctrine()->getManager();
            $commande=$em->getRepository(Commande::class)->find($id);
            $commande->setEtat($etat);
            if ($etat==2)
            {
                $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($commande);
                foreach ($pcs as $pc)
                {
                    if ($pc->getProduit()->getQuantiteProd()-$pc->getQuantite()>=0)
                        $pc->getProduit()->setQuantiteProd($pc->getProduit()->getQuantiteProd()-$pc->getQuantite());
                    else
                        return $this->redirectToRoute('list_commande');
                }
            }
            $em->flush();
        }
        return $this->redirectToRoute('list_commande');
    }

    public function deleteCommandeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $commande=$em->getRepository(Commande::class)->find($id);
        $pcs=$em->getRepository(ProduitCommande::class)->findByCommande($commande);
        foreach ($pcs as $pc)
            $em->remove($pc);
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('list_commande');
    }

    public function statsCommandeAction()
    {
        return $this->render('@Commande/back/stats.html.twig');
    }
}
