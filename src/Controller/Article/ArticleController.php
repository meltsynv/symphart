<?php


namespace App\Controller\Article;


use App\Entity\Article;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('Article/index.html.twig', array('articles' => $articles));
    }

    /**
     * @Route("/article/new", name="newArticle")
     * @Method({"GET", "POST"})
     */
    public function newArticle(Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('Article/newArticle.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/edit/{id}", name="editArticle")
     * @Method({"GET", "POST"})
     */
    public function editArticle(Request $request, $id)
    {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('Article/editArticle.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/{id}", name="articleShow")
     */
    public function showArticle($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('Article/showArticle.html.twig', array('article' => $article));
    }

    /**
     * @Route("/article/delete/{id}")
     * @Method({"DELETE"})
     */
    public function deleteArticle(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }
}