<?php

namespace AnimalBundle\Controller;

use AnimalBundle\Entity\Animal;
use AnimalBundle\Entity\CategorieAnimal;
use AnimalBundle\Entity\LikeAimal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Animal controller.
 *
 */
class AnimalController extends Controller
{
    /**
     * Lists all animal entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $animals = $em->getRepository('AnimalBundle:Animal')->findAll();

        return $this->render('animal/index.html.twig', array(
            'animals' => $animals,
        ));
    }

    /**
     * Creates a new animal entity.
     *
     */
    public function newAction(Request $request)
    {
        $animal = new Animal();
        $form = $this->createForm('AnimalBundle\Form\AnimalType', $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $animal->UploadProfilePicture();
            $em->persist($animal);
            $em->flush();

            return $this->redirectToRoute('animal_show', array('id' => $animal->getId()));
        }

        return $this->render('animal/new.html.twig', array(
            'animal' => $animal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a animal entity.
     *
     */
    public function showAction(Animal $animal)
    {
        $deleteForm = $this->createDeleteForm($animal);

        return $this->render('animal/show.html.twig', array(
            'animal' => $animal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing animal entity.
     *
     */
    public function editAction(Request $request, Animal $animal)
    {
        $deleteForm = $this->createDeleteForm($animal);
        $editForm = $this->createForm('AnimalBundle\Form\AnimalType', $animal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('animal_edit', array('id' => $animal->getId()));
        }

        return $this->render('animal/edit.html.twig', array(
            'animal' => $animal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a animal entity.
     *
     */
    public function deleteAction(Request $request, Animal $animal)
    {
        $form = $this->createDeleteForm($animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($animal);
            $em->flush();
        }

        return $this->redirectToRoute('animal_index');
    }

    /**
     * Creates a form to delete a animal entity.
     *
     * @param Animal $animal The animal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Animal $animal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('animal_delete', array('id' => $animal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function listAction()
    {
        $em=$this->getDoctrine()->getManager();
        $animaux=$em->getRepository(Animal::class)->findAll();
        $categories=$em->getRepository(CategorieAnimal::class)->findAll();
        return $this->render('animal/animaux.html.twig', array('animaux'=>$animaux, 'categories'=>$categories));
    }

    public function detailsAction($id)
    {
        $animal=$this->getDoctrine()->getRepository(Animal::class)->find($id);
        $likes=$this->getDoctrine()->getRepository(LikeAimal::class)->findByAnimal($animal);
        if ($this->getUser()!=null)
        {
            $like=$this->getDoctrine()->getRepository(LikeAimal::class)->findOneBy(array('user'=>$this->getUser(), 'animal'=>$animal));
            if ($like==null)
                $isLike=0;
            else
                $isLike=1;
        }
        else
            $isLike=0;

        if ($likes==null)
            $nbLikes=0;
        else
            $nbLikes=count($likes);

        return $this->render('animal/details.html.twig', array('animal'=>$animal, 'nbLikes'=>$nbLikes, 'isLike'=>$isLike));
    }

    public function likeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        if ($user==null)
            return $this->redirectToRoute('login');
        $animal=$em->getRepository(Animal::class)->find($id);
        $like=$em->getRepository(LikeAimal::class)->findOneBy(array('user'=>$user, 'animal'=>$animal));
        if ($like==null)
        {
            $like=new LikeAimal();
            $like->setUser($user);
            $like->setAnimal($animal);
            $em->persist($like);
        }
        else
            $em->remove($like);
        $em->flush();
        $likes=$em->getRepository(LikeAimal::class)->findByAnimal($animal);
        if ($likes==null)
            return new Response(0);
        return new Response(count($likes));
    }
}
