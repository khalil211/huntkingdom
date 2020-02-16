<?php

namespace GestionProduitBundle\Controller;

use GestionProduitBundle\Entity\CategorieProduit;
use GestionProduitBundle\Entity\Produit;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $em2=$this->getDoctrine()->getManager();
        $categories=$em2->getRepository('GestionProduitBundle:CategorieProduit')->findAll();
         $listeproduits = $em->getRepository('GestionProduitBundle:Produit')->findAll();
         $produits  = $this->get('knp_paginator')->paginate(
        $listeproduits,
        $request->query->get('page', 1)/*le numéro de la page à afficher*/,
        5/*nbre d'éléments par page*/
    );
        return $this->render('produit/shop.html.twig', array(
            'produits' => $produits,
            'categories'=>$categories,
        ));



    }

    public function detailsAction(Produit $produit){

        return $this->render('produit/details.html.twig', array(
            'produit' => $produit,
            ));
    }
    public function categoriesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em2=$this->getDoctrine()->getManager();
        $categories=$em2->getRepository('GestionProduitBundle:CategorieProduit')->findAll();
        $produits = $em->getRepository('GestionProduitBundle:Produit')->findByCategorie( $em->getRepository(CategorieProduit::class)->find($id));

        return $this->render('produit/shop.html.twig', array(
            'produits' => $produits,
            'categories'=>$categories,
        ));
    }

  /*  public function searchProdAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $produits=$em->getRepository('GestionProduitBundle:Produit')->findEntitiesByString($requestString);
        if (!$produits){
            $result['produits']['error']="Produit introuvable";
        }
        else{
            $result['produits']=$this->getRealEntites($produits);

        }
        return new Response(json_encode($result));
    }

    public function getRealEntites($produits){
        foreach ($produits as $produits){
        $realEntities[$produits->getId()]=[$produits->getImageProd(),$produits->getNomProd()];
    }
    return $realEntities;
}*/
}
