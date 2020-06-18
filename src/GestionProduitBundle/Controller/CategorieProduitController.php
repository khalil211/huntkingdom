<?php

namespace GestionProduitBundle\Controller;

use GestionProduitBundle\Entity\CategorieProduit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Categorieproduit controller.
 *
 */
class CategorieProduitController extends Controller
{
    /**
     * Lists all categorieProduit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categorieProduits = $em->getRepository('GestionProduitBundle:CategorieProduit')->findAll();

        return $this->render('categorieproduit/index.html.twig', array(
            'categorieProduits' => $categorieProduits,
        ));
    }

    /**
     * Creates a new categorieProduit entity.
     *
     */
    public function newAction(Request $request)
    {
        $categorieProduit = new Categorieproduit();
        $form = $this->createForm('GestionProduitBundle\Form\CategorieProduitType', $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorieProduit);
            $em->flush();

            return $this->redirectToRoute('categorieproduit_show', array('id' => $categorieProduit->getId()));
        }

        return $this->render('categorieproduit/new.html.twig', array(
            'categorieProduit' => $categorieProduit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a categorieProduit entity.
     *
     */
    public function showAction(CategorieProduit $categorieProduit)
    {
        $deleteForm = $this->createDeleteForm($categorieProduit);

        return $this->render('categorieproduit/show.html.twig', array(
            'categorieProduit' => $categorieProduit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing categorieProduit entity.
     *
     */
    public function editAction(Request $request, CategorieProduit $categorieProduit)
    {
        $deleteForm = $this->createDeleteForm($categorieProduit);
        $editForm = $this->createForm('GestionProduitBundle\Form\CategorieProduitType', $categorieProduit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorieproduit_edit', array('id' => $categorieProduit->getId()));
        }

        return $this->render('categorieproduit/edit.html.twig', array(
            'categorieProduit' => $categorieProduit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a categorieProduit entity.
     *
     */
    public function deleteAction(Request $request, CategorieProduit $categorieProduit)
    {
        $form = $this->createDeleteForm($categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorieProduit);
            $em->flush();
        }

        return $this->redirectToRoute('categorieproduit_index');
    }

    /**
     * Creates a form to delete a categorieProduit entity.
     *
     * @param CategorieProduit $categorieProduit The categorieProduit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CategorieProduit $categorieProduit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorieproduit_delete', array('id' => $categorieProduit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function getAllAction()
    {
        $categories=$this->getDoctrine()->getRepository(CategorieProduit::class)->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($categories);
        return new JsonResponse($formatted);
    }
}
