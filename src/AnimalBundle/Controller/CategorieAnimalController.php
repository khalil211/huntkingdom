<?php

namespace AnimalBundle\Controller;

use AnimalBundle\Entity\CategorieAnimal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorieanimal controller.
 *
 */
class CategorieAnimalController extends Controller
{
    /**
     * Lists all categorieAnimal entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categorieAnimals = $em->getRepository('AnimalBundle:CategorieAnimal')->findAll();

        return $this->render('categorieanimal/index.html.twig', array(
            'categorieAnimals' => $categorieAnimals,
        ));
    }

    /**
     * Creates a new categorieAnimal entity.
     *
     */
    public function newAction(Request $request)
    {
        $categorieAnimal = new Categorieanimal();
        $form = $this->createForm('AnimalBundle\Form\CategorieAnimalType', $categorieAnimal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorieAnimal);
            $em->flush();

            return $this->redirectToRoute('categorieanimal_show', array('id' => $categorieAnimal->getId()));
        }

        return $this->render('categorieanimal/new.html.twig', array(
            'categorieAnimal' => $categorieAnimal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a categorieAnimal entity.
     *
     */
    public function showAction(CategorieAnimal $categorieAnimal)
    {
        $deleteForm = $this->createDeleteForm($categorieAnimal);

        return $this->render('categorieanimal/show.html.twig', array(
            'categorieAnimal' => $categorieAnimal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing categorieAnimal entity.
     *
     */
    public function editAction(Request $request, CategorieAnimal $categorieAnimal)
    {
        $deleteForm = $this->createDeleteForm($categorieAnimal);
        $editForm = $this->createForm('AnimalBundle\Form\CategorieAnimalType', $categorieAnimal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorieanimal_edit', array('id' => $categorieAnimal->getId()));
        }

        return $this->render('categorieanimal/edit.html.twig', array(
            'categorieAnimal' => $categorieAnimal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a categorieAnimal entity.
     *
     */
    public function deleteAction(Request $request, CategorieAnimal $categorieAnimal)
    {
        $form = $this->createDeleteForm($categorieAnimal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorieAnimal);
            $em->flush();
        }

        return $this->redirectToRoute('categorieanimal_index');
    }

    /**
     * Creates a form to delete a categorieAnimal entity.
     *
     * @param CategorieAnimal $categorieAnimal The categorieAnimal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CategorieAnimal $categorieAnimal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorieanimal_delete', array('id' => $categorieAnimal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
