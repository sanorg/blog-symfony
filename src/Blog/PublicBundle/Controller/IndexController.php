<?php

namespace Blog\PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('BlogPublicBundle:Index:index.html.twig');
    }
}
