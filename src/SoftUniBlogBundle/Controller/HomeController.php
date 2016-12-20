<?php

namespace SoftUniBlogBundle\Controller;

use Doctrine\ORM\Mapping\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\Comment;
use SoftUniBlogBundle\Entity\Tag;
use SoftUniBlogBundle\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index", defaults={"page" = 1})
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {


        $em = $this->getDoctrine()
            ->getManager();

        /**
         * @var $articles = Article[]
         */
        $articles = $this->getDoctrine()->getRepository(Article::class)->
        findBy(array(),array('dateAdded' => 'DESC'));

        /**
         * @var paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );

        $tags = $em->getRepository(Tag::class)
            ->getTags();

        $tagWeights = $em->getRepository(Tag::class)
            ->getTagWeights($tags);

        return $this->render('blog/index.html.twig', [
            'articles' => $result,
            'tags' => $tagWeights


        ]);
    }










}
