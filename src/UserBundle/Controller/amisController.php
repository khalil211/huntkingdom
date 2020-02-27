<?php

namespace UserBundle\Controller;

use UserBundle\Entity\amis;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * Ami controller.
 *
 * @Route("amis")
 */
class amisController extends Controller
{
    /**
     * Lists all ami entities.
     *
     * @Route("/", name="amis_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $amis = $em->getRepository('UserBundle:amis')->findAll();

        return $this->render('amis/index.html.twig', array(
            'amis' => $amis,
        ));
    }

    /**
     * Creates a new ami entity.
     *
     * @Route("/new", name="amis_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $idfriend)
    {
        $amis = new amis();
        $user=$this->getUser();
        $amis->setFirstuser($user->getId());
        $amis->setSeconduser($idfriend);
        //$amis->setSeconduser(4);
        $amis->setEtat(0); // 0 == pending, 1 == rejected
        $amis->setActionuser($user->getId());

        $reverse = new amis ();
        $reverse->setFirstuser($amis->getSeconduser());
        $reverse->setSeconduser($amis->getFirstuser());
        $reverse->setEtat(0); // 0 == pending, 1 == rejected
        $reverse->setActionuser($user->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($amis);
            $em->flush();

            $rev = $this->getDoctrine()->getManager();
            $rev->persist($reverse);
            $rev->flush();

            return $this->redirectToRoute('feed');
       // }

        /*
        return $this->render('amis/new.html.twig', array(
            'ami' => $amis,
            'form' => $form->createView(),
        ));
        */
    }

    public function acceptAction($amiid)
    {
        $amis = $this->getDoctrine()->getRepository(amis::class)->find($amiid);
        $user=$this->getUser();
        //$amis->setActionuser($user->getId());
        $amis->setEtat(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($amis);
        $em->flush();

        return $this->redirectToRoute('feed');
    }

    public function declineAction($amiid)
    {
        $amis = $this->getDoctrine()->getRepository(amis::class)->find($amiid);
        $user=$this->getUser();
        //$amis->setActionuser($user->getId());
        $amis->setEtat(2);
        $em = $this->getDoctrine()->getManager();
        $em->persist($amis);
        $em->flush();

        return $this->redirectToRoute('feed');
    }

    /**
     * Finds and displays a ami entity.
     *
     * @Route("/{id}", name="amis_show")
     * @Method("GET")
     */
    public function showAction(amis $ami)
    {
        $deleteForm = $this->createDeleteForm($ami);

        return $this->render('amis/show.html.twig', array(
            'ami' => $ami,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ami entity.
     *
     * @Route("/{id}/edit", name="amis_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, amis $ami)
    {
        $deleteForm = $this->createDeleteForm($ami);
        $editForm = $this->createForm('UserBundle\Form\amisType', $ami);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('amis_edit', array('id' => $ami->getId()));
        }

        return $this->render('amis/edit.html.twig', array(
            'ami' => $ami,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function invitationsAction()
    {
        $user=$this->getUser();

        if ($user==null)
            return $this->redirect('login');
        $first=$user->getId();
        //$publications=$this->getDoctrine()->getRepository(publication::class)->findAll();
        //$amis=$this->getDoctrine()->getRepository(amis::class)->findAll();

        //$amis=$this->getDoctrine()->getRepository(amis::class)->findBy(  array('actionuser' => $first));
        $amis=$this->getDoctrine()->getRepository(amis::class)->getAmis($first);
        $friends=Array();
        foreach ($amis as $ami)
        {
            $user=$this->get('fos_user.user_manager')->findUserBy(array('id'=>$ami->getSecondUser()));
            $friends[$user->getId()]=$user->getUsername();
        }
        return $this->render('@User/invitations.html.twig', array('amis'=>$amis, 'friends'=> $friends));
    }

    /**
     * Deletes a ami entity.
     *
     * @Route("/{id}", name="amis_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, amis $ami)
    {
        $form = $this->createDeleteForm($ami);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ami);
            $em->flush();
        }

        return $this->redirectToRoute('amis_index');
    }

    /**
     * Creates a form to delete a ami entity.
     *
     * @param amis $ami The ami entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(amis $ami)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('amis_delete', array('id' => $ami->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
