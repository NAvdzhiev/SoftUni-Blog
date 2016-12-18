<?php

namespace SoftUniBlogBundle\Controller;

use Doctrine\ORM\Mapping\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\Tag;
use SoftUniBlogBundle\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /**
         * @var $articles = Article[]
         */
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(array(),array('dateAdded' => 'DESC'));

        return $this->render('blog/index.html.twig', ['articles' => $articles]);
    }


}
