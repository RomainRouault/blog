<?php
/**
*Class PostController provides methodes for all related post features
*
*/

namespace Blog\Controller;

use Blog\Model\PostManager;
use Blog\Model\Entity\Post;

class PostController extends Controller
{   

    /**
    *display published article in blog for frontend
    *
    * @return object Twig
    * @throws Exception
    * @throws Twig_Error
    */
    public function blog()
    {       
            //call manager
           $postManager = new PostManager();
           $blogPosts = $postManager->getPublishedPosts();

            if ($blogPosts->rowCount() === 0) 
            {
                throw new \Exception('Il n\'y a pas encore d\'article publié');
            }

            //call view
            echo $this->twig->render('blogView.html.twig', array('blogPosts' => $blogPosts));
    }

    /**
    *display form for adding post in backend
    *
    * @return object Twig
    */
    public function addPostForm()
    {   
        //call view
        echo $this->twig->render('addPostForm.twig');

    }

    /**
    *check and add new post to DB
    *
    * @return boolean
    */
    public function addPost()
    {   
        if (!empty($_POST['postTitle']) && !empty($_POST['postChapo']) && !empty($_POST['postContent']))
        {
            $post = new Post($_POST);
            $postManager = new PostManager();

            $affectedlines = $postManager->addPost($post);

            if ($affectedlines === false) 
            {
                throw new \Exception('Impossible d\'ajouter l\'article');
            }

            else 
            {
                header('Location: ../');
            }


        }

        else
        {
            throw new \Exception('Merci de remplir la totalité des champs du formulaire');
        }
    }

    /**
    *display view for administrator panel
    *
    * @return object Twig
    */
    public function postsList()
    {
        //call manager
       $postManager = new PostManager();
       $postsList = $postManager->getAllPostsList();

        if ($postsList->rowCount() === 0) 
        {
            throw new \Exception('Il n\'y a pas encore d\'article publié');
        }

        //call view
        echo $this->twig->render('postslist.twig', array('postsList' => $postsList));
    }


}
