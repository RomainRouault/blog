<?php

namespace Blog\Controller;

use Blog\Model\PostManager;

class FrontEndController
{
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem('./view/front');
        $twig = new \Twig_Environment($this->$loader);
    }

    public function blog()
    {
        if (empty($blogPosts)) {
            throw new Exception('Il n\'y a pas encore d\'article publié');
        }

        return $twig->render('blogView.html.twig', array('blogPost' => $blogPosts));
    }
}
