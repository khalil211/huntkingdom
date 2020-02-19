<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\commentaire;
use UserBundle\Entity\publication;
use UserBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    public function profileAction($username)
    {
        $user=$this->getUser();
        if ($user==null)
        {return $this->redirect('login');}
        $publications=$this->getDoctrine()->getRepository(publication::class)->findBy(  array('user' => $user),
            array('datepublication' => 'asc'),
            );

        return $this->render('@User/profile.html.twig', array('publications'=>$publications));
    }

    public function feedAction()
    {
        $user=$this->getUser();
        if ($user==null)
            return $this->redirect('login');
        $publications=$this->getDoctrine()->getRepository(publication::class)->findAll();

        return $this->render('@User/feed.html.twig', array('publications'=>$publications));
    }

    public function publicationAction($pub)
    {
        $publications=$this->getDoctrine()->getRepository(publication::class)->findBy(  array('id' => $pub),
            array('datepublication' => 'asc'),
            );
        $commentaires=$this->getDoctrine()->getRepository(commentaire::class)->findBy(  array('publication' => $pub),
            array('datecommentaire' => 'asc'),
            );

        $counter=count($commentaires);

        return $this->render('@User/publication.html.twig', array('publications'=>$publications,
            'commentaires'=>$commentaires,'nb'=>$counter));
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
}

