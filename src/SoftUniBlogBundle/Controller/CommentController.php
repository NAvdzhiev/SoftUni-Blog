<?php

namespace SoftUniBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SoftUniBlogBundle\Entity\Comment;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * @Route("/comment/form", name="comment_form")
     *
     * @param $article_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction($article_id)
    {
        $articles = $this->getArticles($article_id);

        $comment = new Comment();
        $comment->setArticles($articles);
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('comment/form.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/comment/create", name="comment_create")
     *
     * @param $article_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction($article_id, Request $request)
    {
        $articles = $this->getArticles($article_id);

        $comment = new Comment();


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isValid()) {
                $comment->setArticles($articles);
                $em = $this->getDoctrine()
                    ->getManager();
                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('article_view', array(
                        'id' => $comment->getArticles()->getId())) .
                        '#comment-' . $comment->getId()
                );
            }
        }

        return $this->render('comment/form.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView()
        ));
    }

    private function getArticles($article_id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $articles = $em->getRepository(Article::class)->find($article_id);

        if (!$articles) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $articles;
    }

}
