<?php

namespace UserBundle\Controller;

//use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\commentaire;
use UserBundle\Entity\Groupe;
use UserBundle\Entity\Interest;
use UserBundle\Entity\Membership;
use UserBundle\Entity\myinterests;
use UserBundle\Entity\publication;
use UserBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Form\publicationType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\amis;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Repository\amisRepository;


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

    public function AllAction()
    {
        $users=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function findAction($id)
    {
        $users=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->find($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function connectAction($username)
    {
        $users=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['username' => $username]);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setUsernameCanonical($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setEmailCanonical($request->get('email'));
        $user->setEnabled(1);
        $user->setPassword($request->get('password'));
        //$user->setRoles('a:0:{}');
        $em->persist($user);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($user);
        return new JsonResponse($formatted);

    }

    public function pubsAction()
    {
        $publications=$this->getDoctrine()->getManager()->getRepository('UserBundle:Publication')->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($publications);
        return new JsonResponse($formatted);
    }

    public function mypubsAction($id)
    {
        $user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $id]);
        $publications=$this->getDoctrine()->getManager()->getRepository('UserBundle:Publication')->findBy(array('user' => $user));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($publications);
        return new JsonResponse($formatted);
    }

    public function pubcommentsAction($id)
    {
        $publication=$this->getDoctrine()->getManager()->getRepository('UserBundle:Publication')->findOneBy(['id' => $id]);
        $comments=$this->getDoctrine()->getManager()->getRepository('UserBundle:Commentaire')->findBy(array('publication' => $publication));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($comments);
        return new JsonResponse($formatted);
    }

    public function pubcommentAction($id)
    {
        $publication=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $id]);
        $comments=$this->getDoctrine()->getManager()->getRepository('UserBundle:Commentaire')->findBy(array('publication' => $publication));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($comments);
        return new JsonResponse($formatted);
    }

    public function createPubAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $request->get('id')]);
        //$user->setId($request->get('id'));
        $publication = new publication();
        $publication->setText($request->get('text'));
        $publication->setUser($user);
        $publication->setTitre("");
        $publication->setImage("");
        $publication->setDatepublication(new \DateTime('now'));
        $em->persist($publication);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($publication);
        return new JsonResponse($formatted);

    }

    public function createcommentAction(Request $request, $id, $pubid)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = new commentaire();
        $user = new User();
        $user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $id]);
        //$user->setId($request->get('id'));
        $publication = new publication();
        //$pubid=$request->get('id');
        $publication = $this->getDoctrine()->getManager()->getRepository('UserBundle:Publication')->findOneBy(['id' => $pubid]);
        $commentaire->setPublication($publication);
        $commentaire->setUser($user);
        $commentaire->setText($request->get('text'));
        $commentaire->setDatecommentaire(new \DateTime('now'));
        $em->persist($commentaire);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($commentaire);
        return new JsonResponse($formatted);
    }

    /*public function findfriendsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ami = new amis();
        $ami=$this->getDoctrine()->getManager()->getRepository('UserBundle:amis')->findOneBy(['firstuser' => $request->get('firstuser')]);
        //$user->setId($request->get('id'));
        $publication = new publication();
        $publication->setText($request->get('text'));
        $publication->setUser($user);
        $publication->setTitre("");
        $publication->setImage("");
        $publication->setDatepublication(new \DateTime('now'));
        $em->persist($publication);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($publication);
        return new JsonResponse($formatted);

    }
*/
    public function updatepasswordAction($id, $password)
    {
        $em = $this->getDoctrine()->getManager();
        //$user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->find($request->get('id'));
        $user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->find($id);
        //$user->setUsername($request->get('username'));
        //$user->setUsernameCanonical($request->get('username'));
        //$user->setEmail($request->get('email'));
        //$user->setEmailCanonical($request->get('email'));
        //$user->setEnabled(1);
        $user->setPassword($password);
        //$user->setRoles('a:0:{}');
        $em->persist($user);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($user);
        return new JsonResponse($formatted);

    }

    public function searchuserAction($searchid)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findbyusername($searchid);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function friendAction($first, $second)
    {
        $em = $this->getDoctrine()->getManager();
        //$user = new User();
        //$user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $request->get('id')]);
        //$user->setId($request->get('id'));
        $amis = new amis();
        $amis2 = new amis();
        $amis->setActionUser($first);
        $amis->setFirstuser($first);
        $amis->setSeconduser($second);
        $amis->setEtat(0);

        $amis2->setActionUser($first);
        $amis2->setFirstuser($second);
        $amis2->setSeconduser($first);
        $amis2->setEtat(0);


        $em->persist($amis);
        $em->persist($amis2);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($amis);
        return new JsonResponse($formatted);

    }

    public function searchfriendAction($first, $second)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findbyusers($first,$second);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function myinvitationsAction($first)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findinvitations($first);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function myfriendsAction($first)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findbyfirstuser($first);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function acceptinvitationsAction($id1, $id2, $des)
    {
        $em = $this->getDoctrine()->getManager();
        //$user = new User();
        //$user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findOneBy(['id' => $request->get('id')]);
        //$user->setId($request->get('id'));
        $amis = $this->getDoctrine()->getRepository(User::class)->findbyusers($id1,$id2);
        $amis2 = $this->getDoctrine()->getRepository(User::class)->findbyusers($id2,$id1);
        foreach ($amis as &$ami) {
            $ami->setEtat($des);
            $em->persist($ami);
        }

        foreach ($amis2 as &$ami) {
            $ami->setEtat($des);
            $em->persist($ami);
        }
        //$amis->setEtat($des);
        //$amis2->setEtat($des);
        //$em->persist($amis);
        //$em->persist($amis2);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($amis);
        return new JsonResponse($formatted);

    }

    public function addinterestAction($text)
    {
        $em = $this->getDoctrine()->getManager();
        $inter = new interest();

        $inter->setInterest($text);
        $em->persist($inter);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($inter);
        return new JsonResponse($formatted);

    }

    public function addmyinterestAction($text, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $inter = new myinterests();

        $inter->setInterest($text);
        $inter->setUserId($id);

        $em->persist($inter);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($inter);
        return new JsonResponse($formatted);

    }

    public function myinterestslistAction($id)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findmyinterests($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function allinterestsAction()
    {
        $users=$this->getDoctrine()->getRepository(Interest::class)->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function createGroupeAction( $name, $desc)
    {
        $em = $this->getDoctrine()->getManager();
        $groupe = new Groupe();

        $groupe->setName($name);
        $groupe->setDescription($desc);
        $em->persist($groupe);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($groupe);
        return new JsonResponse($formatted);
    }

    public function addmemberAction( $id, $gid, $role)
    {
        $em = $this->getDoctrine()->getManager();
        $membership = new Membership();

        $membership->setUserId($id);
        $membership->setGroupId($gid);
        $membership->setRole($role);
        $em->persist($membership);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($membership);
        return new JsonResponse($formatted);
    }

    public function mygroupeslistAction($id)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findmygroupes($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function allgroupesAction()
    {
        $groupes=$this->getDoctrine()->getRepository(Groupe::class)->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($groupes);
        return new JsonResponse($formatted);
    }

    public function findgroupeAction($id)
    {
        $users=$this->getDoctrine()->getManager()->getRepository('UserBundle:Groupe')->find($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function findgroupepubAction($id)
    {
        $users=$this->getDoctrine()->getManager()->getRepository(User::class)->findGroupepub($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function getmembersAction($id)
    {
        $users=$this->getDoctrine()->getManager()->getRepository(User::class)->findmembers($id);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function memberAction($id, $gid)
    {
        $users=$this->getDoctrine()->getManager()->getRepository(User::class)->ismember($id, $gid);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($users);
        return new JsonResponse($formatted);
    }

    public function grppubAction( $id , $gid, $text)
    {
        $em = $this->getDoctrine()->getManager();
        $publication = new publication();
        $user=$this->getDoctrine()->getManager()->getRepository('UserBundle:User')->find($id);
        $publication->setUser($user);
        $publication->setGroupeId($gid);
        $publication->setText($text);
        $publication->setTitre("");
        $publication->setImage("");
        $publication->setDatepublication(new \DateTime('now'));
        $em->persist($publication);
        $em->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($publication);
        return new JsonResponse($formatted);
    }
}

