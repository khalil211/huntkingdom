<?php

namespace UserBundle\Controller;

//use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\commentaire;
use UserBundle\Entity\publication;
use UserBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Form\publicationType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\amis;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    public function profileAction($username)
    {
        $user=$this->get('fos_user.user_manager')->findUserByUsername($username);
        if ($user==null)
            $user = $this->getDoctrine()->getRepository(User::class)->find($username);

        $publications=$this->getDoctrine()->getRepository(publication::class)->findBy(  array('user' => $user),
            array('datepublication' => 'desc')
            );

        $identifiant=$user->getId();
        //$amis=$this->getDoctrine()->getRepository(amis::class)->findBy( array('firstuser' => $identifiant));

        //$first=$user->getId();

        $amis=$this->getDoctrine()->getRepository(amis::class)->getFriends($identifiant);
        $counter=count($publications);
        $counter2=count($amis);

        $friends=Array();

        foreach ($amis as $ami)
        {
            $userf=$this->get('fos_user.user_manager')->findUserBy(array('id'=>$ami->getSecondUser()));
            $friends[$userf->getId()]=$userf->getUsername();
        }

        return $this->render('@User/profile.html.twig', array('publications'=>$publications,'amis'=>$amis,'user'=>$user,'nb'=>$counter,'friendsnumber'=>$counter2, 'friends'=> $friends));
    }




    public function searchforAction($searchid)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findbyusername($searchid);
        return $this->render('@User/search.html.twig', array('users'=>$users));
    }
    //not in use anymore
    public function feedAction()
    {
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $publications=$this->getDoctrine()->getRepository(publication::class)->findAll();

        return $this->render('@User/feed.html.twig', array('publications'=>$publications));
    }




    public function publicationAction(Request $request, $pub)
    {
        $publications=$this->getDoctrine()->getRepository(publication::class)->findBy(  array('id' => $pub),
            array('datepublication' => 'asc')
            );
        $commentaires=$this->getDoctrine()->getRepository(commentaire::class)->findBy(  array('publication' => $pub),
            array('datecommentaire' => 'asc')
            );
        $counter=count($commentaires);

        $commentaire = new Commentaire();
        $form = $this->createForm('UserBundle\Form\commentaireType', $commentaire);
        $form->handleRequest($request);
        $commentaire->setDatecommentaire(new \DateTime('now'));
        $commentaire->setUser($this->getUser());

        //$publications=$this->getDoctrine()->getRepository(publication::class)->findBy( array('id' => $pub));
        $repo = $this->getDoctrine()->getRepository('UserBundle:publication');
        $found = $repo->find($pub);
        $commentaire->setPublication($found);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('feed', array('id' => $commentaire->getId()));
        }

        return $this->render('@User/publication.html.twig', array('publications'=>$publications,
            'commentaires'=>$commentaires,'nb'=>$counter,'form' => $form->createView()));
    }

   /* public function utilisateursAction()
    {
        return $this->render('@User/listutilisateurs.html.twig');
    }
*/

    public function utilisateursAction()
    {
        $utilisateurs=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('@User/listutilisateurs.html.twig', array('utilisateurs'=>$utilisateurs));
    }

    public function publierAction(Request $request)
    {
        $user=$this->get('fos_user.user_manager')->findUserByUsername($username);
        $publication = new publication();
        $form = $this->createForm('UserBundle\Form\publicationType', $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute('feed');
        }

        return $this->render('profile.html.twig', array(
            //'publications'=>$publications,
            'user'=>$user,
            'form' => $form->createView(),
        ));
    }
}

