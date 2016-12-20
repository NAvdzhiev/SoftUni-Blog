<?php

namespace SoftUniBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TagController extends Controller
{
    /**
     * Matches /tags/{name} exactly
     *
     * @Route("/tags/{name}", name="articles_with_tag", defaults={"page"=1})
     * @Method("GET")
     * @param $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articles($name)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->findOneBy(['name' => $name]);



        return $this->render('tags/articles.html.twig', ['tag' => $tag]);

    }


}
