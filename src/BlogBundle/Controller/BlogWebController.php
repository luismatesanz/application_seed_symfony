<?php

namespace BlogBundle\Controller;

use BlogBundle\Handler\BlogHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BlogWebController extends Controller
{
    /**
     * Get Handler Controller.
     **/
    private function getHandler() : BlogHandler
    {
        return $this->get('handler.blog');
    }

    /**
     * @Route("/", name="blogHomepage")
     */
    public function indexAction()
    {
        $blogs = $this->getHandler()->findAllBy(array());
        return $this->render('BlogBundle:Default:index.html.twig', array('blogs' => $blogs));
    }
}
