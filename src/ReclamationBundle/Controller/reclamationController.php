<?php

namespace ReclamationBundle\Controller;

use ReclamationBundle\Entity\reclamation;
use ReclamationBundle\Form\reclamationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class reclamationController extends Controller
{

    public function AjoutAction()
    {
        return $this->render('@Reclamation/Default/create.html.twig');
    }

    public function AfficherAction()
    {
        return $this->render('@Reclamation/Default/read.html.twig');
    }

    public function readAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reclamation = $em->getRepository('ReclamationBundle:reclamation')->findAll();
        return $this->render('@Reclamation/Default/read.html.twig',array('reclamations'=> $reclamation));
    }

    public function createAction(Request $request)
    {
        $user=$this->getUser();
        if($user==null)
            return $this->redirectToRoute('fos_user_security_login');
        $email=$user->getEmail();
        $reclamation= new reclamation();
        $reclamation->setDateCreation(new \DateTime('now'));
        $reclamation->setUser($user);
        $form = $this->createForm(reclamationType::class, $reclamation);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
            $message = (new \Swift_Message('Réclamation'))
                ->setFrom('noreplyhuntkingdom@gmail.com')
                ->setTo($email)
                ->setBody('Réclamation bien réçu :)' );

            $this->get('mailer')->send($message);
            return $this->redirectToRoute('readreclamation');
        }

        return $this->render('@Reclamation/Default/create.html.twig', array(
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ));
    }
    function deleteAction($id){
        $em=$this->getDoctrine()->getManager();
        $reclamation=$this->getDoctrine()->getRepository(reclamation::class)
            ->find($id);
        $em->remove($reclamation);
        $em->flush();
        return $this->redirectToRoute('readreclamation');
    }

    public function  updateAction(Request $request, $id){
        $em=$this->getDoctrine()->getRepository(reclamation::class);
        $reclamation=$this->getDoctrine()
            ->getRepository(reclamation::class)
            ->find($id);
        $Form=$this->createForm(reclamationType::class,$reclamation);
        $Form->handleRequest($request);
        if($Form->isSubmitted() ){
            $em->modifier($id,$Form->get('type')->getData(),$Form->get('description')->getData(),$Form->get('fichier')->getData());
            return $this->redirectToRoute('readreclamation');

        }
        return $this->render('@Reclamation/Default/update.html.twig',
            array('form'=>$Form->createView()));
    }

    public function  consulteAction($id)
    {

        $em=$this->getDoctrine()->getRepository(reclamation::class)->findBy(array('id'=>$id));

        return $this->render('@Reclamation/Default/consult.html.twig',array('reclamation'=> $em));
    }

//    public function  updateAction(Request $request, $id){
//        $em=$this->getDoctrine()->getManager();
//        $reclamation=$this->getDoctrine()
//            ->getRepository(reclamation::class)
//            ->find($id);
//        $Form=$this->createForm(reclamationType::class,$reclamation);
//        $Form->handleRequest($request);
//        if($Form->isSubmitted() ){
//            $em->flush();
//            return $this->redirectToRoute('readreclamation');
//
//        }
//        return $this->render('@Reclamation/Default/update.html.twig',
//            array('form'=>$Form->createView()));
//    }


    public function afficherrecAction()
    {
        $em=$this->getDoctrine()->getRepository(reclamation::class)->findAll();
        return $this->render('@Reclamation/Dashboard/dashboard.html.twig',array('reclamations'=> $em));
    }

    public function  consulterecAction($id)
    {

        $em=$this->getDoctrine()->getRepository(reclamation::class)->findBy(array('id'=>$id));

        return $this->render('@Reclamation/Dashboard/dashboardConsult.html.twig',array('reclamation'=> $em));
    }

    function deleterecAction($id){
        $em=$this->getDoctrine()->getManager();
        $reclamation=$this->getDoctrine()->getRepository(reclamation::class)
            ->find($id);
        $em->remove($reclamation);
        $em->flush();
        return $this->redirectToRoute('read');
    }
    public function  traiterAction(Request $request, $id){
        $em=$this->getDoctrine()->getRepository(reclamation::class)->findBy(array('id'=>$id));

        return $this->render('@Reclamation/Dashboard/dashboardtraiter.html.twig',array('reclamation'=> $em));
    }



}
