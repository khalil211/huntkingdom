<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\ProduitCommande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BackController extends Controller
{
    public function listCommandeAction()
    {
        $commandes=$this->getDoctrine()->getRepository(Commande::class)->getCommandes();
        $pcs=Array();
        foreach ($commandes as $commande)
            $pcs[$commande->getId()]=$this->getDoctrine()->getRepository(ProduitCommande::class)->findByCommande($commande);
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
                /*$message = (new \Swift_Message('Commande passée'))
                    ->setFrom('noreplyhuntkingdom@gmail.com')
                    ->setTo($commande->getUser()->getEmail())
                    ->setBody(
                        $this->renderView('@Commande/back/facture.html.twig', array('commande'=>$commande, 'pcs'=>$pcs)),
                        'text/html'
                    );
                $this->get('mailer')->send($message);*/
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
        $em=$this->getDoctrine()->getManager();
        // pie chart etat commande
        $commandes=$em->getRepository(Commande::class)->findByEtat(1);
        if ($commandes==null)
            $etats[0]=0;
        else
            $etats[0]=count($commandes);
        $commandes=$em->getRepository(Commande::class)->findByEtat(2);
        if ($commandes==null)
            $etats[1]=0;
        else
            $etats[1]=count($commandes);
        $commandes=$em->getRepository(Commande::class)->findByEtat(3);
        if ($commandes==null)
            $etats[2]=0;
        else
            $etats[2]=count($commandes);
        // pie chart etat commande fin
        return $this->render('@Commande/back/stats.html.twig', array('etats'=>$etats));
    }

    public function pdfCommandeAction($id)
    {
        $commande=$this->getDoctrine()->getRepository(Commande::class)->find($id);
        $pcs=$this->getDoctrine()->getRepository(ProduitCommande::class)->findByCommande($commande);
        $snappy = $this->get('knp_snappy.pdf');
        $html = $this->renderView('@Commande/back/facture.html.twig', array('commande'=>$commande, 'pcs'=>$pcs));
        $filename = 'facture'.$commande->getId().'.pdf';
        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }
}
