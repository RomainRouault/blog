<?php

namespace Blog\Controller;

use Blog\Model\PostManager;

class PostController extends Controller
{   

    //display published article in blog for frontend.
    public function blog()
    {       
            //call manager
           $postManager = new PostManager();
           $blogPosts = $postManager->getPublishedPosts();

            if ($blogPosts->rowCount() === 0) {
                throw new \Exception('Il n\'y a pas encore d\'article publié');
            }

            //call view
            echo $this->twig->render('blogView.html.twig', array('blogPosts' => $blogPosts));
    }

    //display form for adding post in backend.
    public function addPostForm()
    {   
        //call view
        echo $this->twig->render('addPostForm.twig');
    }
}
