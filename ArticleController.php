<?php

namespace SoftUniBlogBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\Comment;
use SoftUniBlogBundle\Entity\Tag;
use SoftUniBlogBundle\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{


    /**
     * @param $em EntityManager
     * @param $tagsString String
     *
     * @return ArrayCollection
     */
    public function getTags($em, $tagsString)
    {
        $tags = explode(",", $tagsString);
        $tagRepo = $this->getDoctrine()->getRepository(Tag::class);
        $tagsToSave = new ArrayCollection();

        foreach ($tags as $tagName){

            $tagName = trim($tagName);
            $tag = $tagRepo->findOneBy(['name' => $tagName]);

            if ($tag == null){
                $tag = new Tag();
                $tag->setName($tagName);
                $em->persist($tag);

            }

            $tagsToSave->add($tag);
        }

        return $tagsToSave;

    }


    /**
     * @param Request $request
     *
     * @Route("/article/create", name="article_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     */
    public function create(Request $request)
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();

            $tagsString = $request->get('tags');
            $tags = $this->getTags($em, $tagsString);

            $article->setAuthor($this->getUser());
            $article->setTags($tags);


            /**@var UploadedFile $file*/
            $file = $article->getCoverPhoto();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $articlesDirectory ='C:\\Users\\DELL\\Desktop\\PHP-Blog\\Blog\\web\\uploads\\articles';

            $file->move(
                $articlesDirectory,
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $article->setCoverPhoto($fileName);
            // ... persist the $product variable or any other work

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('blog_index');
        }




        return $this->render('article/create.html.twig',
            array('form' => $form->createView()));
                    

    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticle($id)
    {


        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $em = $this->getDoctrine()->getManager();

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository(Comment::class)
            ->getCommentsForBlog($article->getId());

        return $this->render('article/article.html.twig', array(
            'article'      => $article,
            'comments'  => $comments
        ));


    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editArticle($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if($article === null){
            return $this->redirectToRoute("blog_index");
        }
        $tags = $article->getTags();

        $tagsString = implode(',' ,$tags->toArray());

        $currentUser = $this->getUser();

        if(!$currentUser->isAuthor($article) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute("blog_index");
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $tagsString = $request->get('tags');
            $tags = $this->getTags($em, $tagsString);

            $article->setTags($tags);

            /**@var UploadedFile $file*/
            $file = $article->getCoverPhoto();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $articlesDirectory ='C:\\Users\\DELL\\Desktop\\PHP-Blog\\Blog\\web\\uploads\\articles';

            $file->move(
                $articlesDirectory,
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $article->setCoverPhoto($fileName);
            // ... persist the $product variable or any other work

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_view', array('id' => $article->getId()));
        }

        return $this->render('article/edit.html.twig',
            array('article' => $article,
                'form' => $form->createView(),
                'tags' => $tagsString));
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if($article === null){
            return $this->redirectToRoute("blog_index");
        }

        $tags = $article->getTags();

        $tagsString = implode(', ', $tags->toArray());


        $currentUser = $this->getUser();

        if(!$currentUser->isAuthor($article) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute("blog_index");
        }



        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {


            $em = $this->getDoctrine()->getManager();

            $em->remove($article);
            $em->flush();



            return $this->redirectToRoute('blog_index');
        }

        return $this->render('article/delete.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
            'tags' => $tagsString,
        ));
    }







}

