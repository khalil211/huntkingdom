<?php

namespace DogBundle\Controller;

use DogBundle\Entity\Chien;
use DogBundle\Entity\Coach;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DogBundle:Default:index.html.twig');
    }

    public function getAllAction()
    {
        $chiens=$this->getDoctrine()->getRepository(Chien::class )->findAll();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($chiens);
        return new JsonResponse($formatted);
    }

    public function getChienAction($userId)
    {
        $chien=$this->getDoctrine()->getRepository(Chien::class )->findByUser($this->getDoctrine()->getRepository(User::class)->find($userId));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($chien);
        return new JsonResponse($formatted);
    }
    public function getChienCoachAction($coachId)
    {
        $chien=$this->getDoctrine()->getRepository(Chien::class )->findByCoach($this->getDoctrine()->getRepository(Coach::class)->find($coachId));
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($chien);
        return new JsonResponse($formatted);
    }
    public function NoteChienAction(Request $request, $id,$note)
    {
        $dog = $this->getDoctrine()->getRepository(Chien::class)->findOneById($id);
        $dog->setNote($note);

        $this->getDoctrine()->getManager()->flush();
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($dog);
        return new JsonResponse($formatted);
    }
    public function HigherAction($coachId)
    {

        $em = $this->getDoctrine()->getManager();
        $em1 = $this->getDoctrine()->getManager();
         $coach=$em1->getRepository(Coach::class)->findOneById($coachId);

        // 2. Setup repository of some entity
        $chiens = $em->getRepository(Chien::class);

        // 3. Query how many rows are there in the Articles table
        $totalArticles = $chiens->createQueryBuilder('c')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(c.id)')
            ->where('c.coach=:coach')
            ->andWhere('c.age > 5')
            ->setParameter('coach', $coach)
            ->getQuery()
            ->getSingleScalarResult();

        // 4. Return a number as response
        // e.g 972
        $coach->setNbr($totalArticles);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($coach);
        return new JsonResponse($formatted);
    }
    public function LowerAction($coachId)
    {

        $em = $this->getDoctrine()->getManager();
        $em1 = $this->getDoctrine()->getManager();
        $coach=$em1->getRepository(Coach::class)->findOneById($coachId);

        // 2. Setup repository of some entity
        $chiens = $em->getRepository(Chien::class);

        // 3. Query how many rows are there in the Articles table
        $totalArticles = $chiens->createQueryBuilder('c')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(c.id)')
            ->where('c.coach=:coach')
            ->andWhere('c.age < 3')
            ->setParameter('coach', $coach)
            ->getQuery()
            ->getSingleScalarResult();

        // 4. Return a number as response
        // e.g 972
        $coach->setNbr($totalArticles);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($coach);
        return new JsonResponse($formatted);
    }
    public function BetweenAction($coachId)
    {

        $em = $this->getDoctrine()->getManager();
        $em1 = $this->getDoctrine()->getManager();
        $coach=$em1->getRepository(Coach::class)->findOneById($coachId);

        // 2. Setup repository of some entity
        $chiens = $em->getRepository(Chien::class);

        // 3. Query how many rows are there in the Articles table
        $totalArticles = $chiens->createQueryBuilder('c')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(c.id)')
            ->where('c.coach=:coach')
            ->andWhere('c.age >=3 and c.age<=5')
            ->setParameter('coach', $coach)
            ->getQuery()
            ->getSingleScalarResult();

        // 4. Return a number as response
        // e.g 972
        $coach->setNbr($totalArticles);
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($coach);
        return new JsonResponse($formatted);
    }
}
