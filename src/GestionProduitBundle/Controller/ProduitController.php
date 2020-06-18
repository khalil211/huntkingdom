<?php

namespace GestionProduitBundle\Controller;

use GestionProduitBundle\Entity\CategorieProduit;
use GestionProduitBundle\Entity\CommentaireProduit;
use GestionProduitBundle\Entity\Produit;
use GestionProduitBundle\Entity\RatingProduit;
use GestionProduitBundle\Form\CommentaireProduitType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Produit controller.
 *
 */
class ProduitController extends Controller
{
    /**
     * Lists all produit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('GestionProduitBundle:Produit')->findAll();

        return $this->render('produit/index.html.twig', array(
            'produits' => $produits,
        ));
    }

    public function listpAction(){

        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('GestionProduitBundle:Produit')->findAll();
        $snappy = $this->get('knp_snappy.pdf');

        $html = $this->renderView('produit/listp.html.twig', array(
            'produits' => $produits,

        ));

        $filename = 'myFirstSnappyPDF';

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }

    /**
     * Creates a new produit entity.
     *
     */
    public function newAction(Request $request)
    {
        $produit = new Produit();
        $form = $this->createForm('GestionProduitBundle\Form\ProduitType', $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $produit->UploadProfilePicture();
            $em->persist($produit);
            $em->flush();

           return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
        }

        return $this->render('produit/new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produit entity.
     *
     */
    public function showAction(Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);

        return $this->render('produit/show.html.twig', array(
            'produit' => $produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produit entity.
     *
     */
    public function editAction(Request $request, Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        $editForm = $this->createForm('GestionProduitBundle\Form\ProduitType', $produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $produit->UploadProfilePicture();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_edit', array('id' => $produit->getId()));
        }

        return $this->render('produit/edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produit entity.
     *
     */
    public function deleteAction(Request $request, Produit $produit)
    {
        $form = $this->createDeleteForm($produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('produit_index');
    }

    /**
     * Creates a form to delete a produit entity.
     *
     * @param Produit $produit The produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Produit $produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produit_delete', array('id' => $produit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function shopAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder=$em->getRepository('GestionProduitBundle:Produit')->createQueryBuilder('p');
        if ($request->query->getAlnum('filter')) {
            $listeproduits=$queryBuilder->where('p.nomProd LIKE :nomProd')
                            ->setParameter('nomProd', '%' . $request->query->getAlnum('filter') . '%')
                            ->getQuery()
                            ->getResult();
        }
        else
            $listeproduits = $em->getRepository('GestionProduitBundle:Produit')->findAll();
        $em2=$this->getDoctrine()->getManager();
        $categories=$em2->getRepository('GestionProduitBundle:CategorieProduit')->findAll();
         $produits  = $this->get('knp_paginator')->paginate(
        $listeproduits,
        $request->query->get('page', 1)/*le numéro de la page à afficher*/,
        6/*nbre d'éléments par page*/
    );
         $produitsR=$em->getRepository(Produit::class)->findAll();
         $notes=array();
         foreach ($produitsR as $p){
             $notesP=$em->getRepository(RatingProduit::class)->findByProduit($p);
             if ($notesP!=null){
                 $somme=0;
                 foreach ($notesP as $noteP)
                     $somme+=$noteP->getNote();
                 $notes[$p->getId()]=round($somme/count($notesP));
             }else
                 $notes[$p->getId()]=0;
         }
         $notesC=$notes;
         $meilleurs=Array();
         $best=Array();
         for ($i=0;$i<3;$i++)
         {
             $idmp=null;
             $nmp=-1;
             foreach($notesC as $idp => $note)
             {
                 if ($note>$nmp)
                 {
                     $nmp=$note;
                     $idmp=$idp;
                 }
             }
             if ($idmp!=null){
                 array_push($meilleurs, $idmp);
                 array_push($best, $em->getRepository(Produit::class)->find($idmp));
                 unset($notesC[$idmp]);
             }
         }
        return $this->render('produit/shop.html.twig', array(
            'produits' => $produits,
            'categories'=>$categories,
            'notes'=>$notes,
            'meilleurs'=>$best
        ));



    }

    public function detailsAction(Request $request,Produit $produit, $id){

        $user=$this->getUser();
        $add_comment = new CommentaireProduit();
        $em = $this->getDoctrine()->getManager();
        $comments=$em->getRepository(CommentaireProduit::class)->findByProduit($produit);
        $add_comment->setProduit($produit);
        $add_comment->setUser($user);
        $add_comment->setDateC( new \DateTime());
        $form = $this->createFormBuilder($add_comment)

            ->add('contenu', TextareaType::class)

            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $add_comment = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($add_comment);
                $em->flush();

                return $this->redirectToRoute('produit_details', array('id' => $produit->getId()));


            }
        }
        $rating=$em->getRepository(RatingProduit::class)->findOneBy(array('user'=>$user, 'produit'=>$produit));
        if ($rating == null)
        {
            $rating=new RatingProduit();
            $rating->setNote(0);
        }
        return $this->render('produit/details.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
            'comment' => $add_comment,
            'comments' => $comments,
            'rating'=>$rating
            ));
    }
    public function categoriesAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $em2=$this->getDoctrine()->getManager();
        $categories=$em2->getRepository('GestionProduitBundle:CategorieProduit')->findAll();
        $listeproduits = $em->getRepository('GestionProduitBundle:Produit')->findByCategorie( $em->getRepository(CategorieProduit::class)->find($id));
        $produits  = $this->get('knp_paginator')->paginate(
            $listeproduits,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            5/*nbre d'éléments par page*/
        );
        $notes=array();
        foreach ($produits as $p){
            $notesP=$em->getRepository(RatingProduit::class)->findByProduit($p);
            if ($notesP!=null){
                $somme=0;
                foreach ($notesP as $noteP)
                    $somme+=$noteP->getNote();
                $notes[$p->getId()]=round($somme/count($notesP));
            }else
                $notes[$p->getId()]=0;
        }
        $notesC=$notes;
        $meilleurs=Array();
        $best=Array();
        for ($i=0;$i<3;$i++)
        {
            $idmp=null;
            $nmp=-1;
            foreach($notesC as $idp => $note)
            {
                if ($note>$nmp)
                {
                    $nmp=$note;
                    $idmp=$idp;
                }
            }
            if ($idmp!=null){
                array_push($meilleurs, $idmp);
                array_push($best, $em->getRepository(Produit::class)->find($idmp));
                unset($notesC[$idmp]);
            }
        }
        return $this->render('produit/shop.html.twig', array(
            'produits' => $produits,
            'categories'=>$categories,
            'notes'=>$notes,
            'meilleurs'=>$best
        ));
    }
    function deleteCAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(CommentaireProduit::class)->find($id);
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute("produit_shop");

    }

    function noterAction($id,$note){
        $user=$this->getUser();
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(Produit::class)->find($id);
        $rating=$em->getRepository(RatingProduit::class)->findOneBy(array('user'=>$user, 'produit'=>$produit));
        if ($rating==null){
            $rating=new RatingProduit();
            $rating->setNote($note);
            $rating->setUser($user);
            $rating->setProduit($produit);
            $em->persist($rating);
        }else
            $rating->setNote($note);
        $em->flush();
        return new Response($note);
    }

    public function getAllAction()
    {
        $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($produits);
        return new JsonResponse($formatted);
    }
}
