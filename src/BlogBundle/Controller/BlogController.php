<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Blog;
use BlogBundle\Entity\CommentaireBlog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Phpml\Classification\NaiveBayes;




use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use http\Env\Response;

/**
 * Blog controller.
 *
 */
class BlogController extends Controller
{
    /**
     * Lists all blog entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $blogs = $em->getRepository('BlogBundle:Blog')->findAll();

        return $this->render('blog/index.html.twig', array(
            'blogs' => $blogs,
        ));
    }

    /**
     * Creates a new blog entity.
     *
     */
    public function newAction(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm('BlogBundle\Form\BlogType', $blog);
        $form->handleRequest($request);
        $blog->setDate( new \DateTime());

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $blog->UploadProfilePicture();

            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute('blog_show', array('id' => $blog->getId()));
        }

        return $this->render('blog/new.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a blog entity.
     *
     */
    public function showAction(Blog $blog)
    {
        $deleteForm = $this->createDeleteForm($blog);

        return $this->render('blog/show.html.twig', array(
            'blog' => $blog,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing blog entity.
     *
     */
    public function editAction(Request $request, Blog $blog)
    {
        $deleteForm = $this->createDeleteForm($blog);
        $editForm = $this->createForm('BlogBundle\Form\BlogType', $blog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $blog->UploadProfilePicture();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_edit', array('id' => $blog->getId()));
        }

        return $this->render('blog/edit.html.twig', array(
            'blog' => $blog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a blog entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository(Blog::class)->find($id);

        $em->remove($blog);
        $em->flush();
        return $this->redirectToRoute('blog_index');

    }
    public function deletecommentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(CommentaireBlog::class)->find($id);
        $blog=$comment->getBlog()->getId();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('blog_details', array('id' => $blog));

    }
    public function viewblogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $listblog = $em->getRepository('BlogBundle:Blog')->findAll();
        $blogs  = $this->get('knp_paginator')->paginate(
            $listblog,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            5/*nbre d'éléments par page*/
        );
        return $this->render('blog/blog.html.twig', array(
            'blogs' => $blogs,

        ));
    }

    public function statAction()
    {
        $pieChart = new PieChart();
        $em = $this->getDoctrine()->getManager();


        $blogs = $em->getRepository(blog::class);
        $vid="Video";
        $im="Image";

        // 3. Query how many rows are there in the Articles table
        $nbrvid = $blogs->createQueryBuilder('a')
            // Filter by some parameter if you want

            ->select('count(a.id)')
            ->Where('a.type = :type')
            ->setParameters([
                'type' => $vid,

            ])
            ->getQuery()
            ->getSingleScalarResult();
        $nbrim = $blogs->createQueryBuilder('a')
            // Filter by some parameter if you want

            ->select('count(a.id)')
            ->Where('a.type = :type')
            ->setParameters([
                'type' => $im,

            ])
            ->getQuery()
            ->getSingleScalarResult();





        $pieChart->getData()->setArrayToDataTable( array(
            ['Blogs', 'Type of blogs'],
            ['Video',     (Int)$nbrvid],
            ['Image',     (Int)$nbrim]
        ));

        $pieChart->getOptions()->setTitle('You still in work');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);





        // 4. Return a number as response
        // e.g 972
        return $this->render('blog/stat.html.twig', array(
            'piechart' => $pieChart,



        ));

    }
    public function detailsAction(Request $request,Blog $blog){

        $user=$this->getUser();
        if($user==null)
            return $this->redirectToRoute('fos_user_security_login');
        $add_comment = new CommentaireBlog();
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository(CommentaireBlog::class)->findByBlog($blog);
        $add_comment->setBlog($blog);
        $add_comment->setUser($user);
        $add_comment->setDate( new \DateTime());

        $form = $this->createFormBuilder($add_comment)

            ->add('contenu', TextareaType::class)

            ->getForm();


        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $text = $form["contenu"]->getData();
                $ftext=\BlogBundle\Helper\ConverHelper::filterwords($text);
                $add_comment->setContenu($ftext);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($add_comment);
                $em->flush();

                return $this->redirectToRoute('blog_details', array('id' => $blog->getId()));
            }
        }


        $reportForm = $this->createForm('BlogBundle\Form\CommentReportForm');
        $reportForm->handleRequest($request);

        return $this->render('blog/details.html.twig', array(
            'form' => $form->createView(),
            'comment' => $add_comment,
            'blog' => $blog,
            'comments'=> $comments,
            /*
             * give the report form a different name in twig
             */
            'report_form' => $reportForm->createView(),
        ));


    }
    public function reportAction(Request $request, $id)
    {
        $reportForm = $this->createForm('BlogBundle\Form\CommentReportForm');
        $reportForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(CommentaireBlog::class)->find($id);
        $blog=$comment->getBlog();
        $email=$comment->getUser()->getUsername();

        /**
         * @var array|string[message, reason]
         */
        $reportData = $reportForm->getData();
        /*
        array( 'reason' => 'value', 'message' => 'value' )
         */


        dump($reportData);

                $message = (new \Swift_Message('Comment Report'))
                ->setFrom('noreplyhuntkingdom@gmail.com')
                ->setTo($comment->getUser()->getEmail())
                ->setBody(
                    $this->renderView('blog/email.html.twig', array(
                        'commande'=>$comment,
                        'blog'=>$blog,
                        'reportdata'=>$reportData,
                        'email'=>$email,

                    )),
                    'text/html'
                );

            $this->get('mailer')->send($message);
            return $this->redirectToRoute('blog_details', array('id' => $blog->getId()));


    }
    public function huntAction(Request $request)
    {
        $huntForm = $this->createForm('BlogBundle\Form\HuntForm');
        $huntForm->handleRequest($request);
        return $this->render('blog/hunt.html.twig', array(
            'huntform' => $huntForm->createView(),
        ));

    }
    public function huntreplyAction(Request $request)
    {

        $huntForm = $this->createForm('BlogBundle\Form\HuntForm');
        $huntForm->handleRequest($request);
        $huntdata = $huntForm->getData();
        dump($huntdata);
        $outlook = $huntForm["outlook"]->getData();
        $temperature = $huntForm["temperature"]->getData();
        $humidity = $huntForm["humidity"]->getData();
        $windy = $huntForm["windy"]->getData();
        dump($outlook);
        dump($temperature);
        dump($humidity);
        dump($windy);

        $samples = [[1, 1, 1, 0], [1, 1, 1, 1], [2, 1, 1, 0], [3, 3, 1, 0], [3, 2, 0, 0], [3, 2, 0, 1], [2, 2, 0, 1], [1, 3, 1, 0], [1, 2, 0, 0], [3, 3, 0, 0], [1, 3, 0, 1], [2, 3, 1, 1], [2, 1, 0, 0], [3, 3, 1, 1]];
        $labels = ['b', 'b', 'a', 'a', 'a', 'b', 'a', 'b', 'a', 'a', 'a', 'a', 'a', 'b'];

        $classifier = new NaiveBayes();
        $classifier->train($samples, $labels);
        $test=$classifier->predict([dump($outlook), dump($temperature), dump($humidity), dump($windy)]);




        return $this->render('blog/huntreply.html.twig', array(
            'outlook' => $outlook,
            'temperature' => $temperature,
            'humidity' => $humidity,
            'windy' => $windy,
            'test'=>$test,

        ));


    }

    /**
     * Creates a form to delete a blog entity.
     *
     * @param Blog $blog The blog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Blog $blog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blog_delete', array('id' => $blog->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
