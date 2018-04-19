<?php

namespace Blog\Controller;

use Blog\Model\PostManager;
use Blog\Model\Entity\Post;

/**
*Class PostController provides methods for all related post features
*
*/
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
           $blogPosts = $postManager->getPostsList();

            if ($blogPosts->rowCount() === 0) 
            {
                throw new \Exception('Il n\'y a pas encore d\'article publié');
            }

            //call view
            echo $this->twig->render('blog_view.twig', array('blogPosts' => $blogPosts));
            unset($_SESSION['message'], $_SESSION['message_origin']);
    }

    /**
    *display form for adding post in backend
    *
    * @return object Twig
    */
    public function addPostForm()
    {   
        //call view
        echo $this->twig->render('add_post_form.twig');

    }

    /**
    *check and add new post to DB
    *
    * @return bool
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
       $postsList = $postManager->getPostsList();

        if ($postsList->rowCount() === 0) 
        {
            throw new \Exception('Il n\'y a pas encore d\'article publié');
        }

        //call view
        echo $this->twig->render('posts_list.twig', array('postsList' => $postsList));
    }

    /**
    *Edit a post
    * 
    * @return mixed
    */
    public function postEdition($postid, $submit)
    {
        $postManager = new PostManager();
        $postData = $postManager->getPost($postid);//check the post id

        if (!empty($postData['idPost']))//if post id is on the DB
        {
            if ($submit) //if edition had been submited
            {   
                if (!empty($_POST['postTitle']) && !empty($_POST['postChapo']) && !empty($_POST['postContent']))
                {
                    $updated_input = new Post($_POST);
                    $edition = $postManager->updatePost($updated_input, $postid);

                    if ($edition)//successful edition
                    {
                        header('Location: ../administrator/');
                    }

                    else
                    {
                        throw new \Exception('Modification de l\'article impossible.');
                    }
                }
                else
                {
                    throw new \Exception('Merci de remplir tout les champs.');
                }

            }

            else //call form view
            {
                echo $this->twig->render('edition_post_form.twig', array('post' => $postData));
            }  
        }

        else
        {
            throw new \Exception('Article inconnu.');
        }
    }

    /**
    *Edit the status of a post
    * 
    * @return bool
    */
    public function postEditionStatus($postid, $status)
    {
        $postManager = new PostManager();
        $status = $postManager->updatePostStatus($postid, $status);

        if ($status)
        {
            header('Location: ../administrator/');
        }

        else
        {
            throw new \Exception('Article inconnu.');
        }
    }

    /**
    *Delete a post
    * 
    * @return bool
    */
    public function deletePost($postid)
    {
        $postManager = new PostManager();
        //check the post id
        $postData = $postManager->getPost($postid);

        //if post id is on the DB
        if (!empty($postData['idPost']))
        {
            $delete = $postManager->deletePost($postid);

            //successful removal
            if ($delete)
            {
                header('Location: ../administrator/');
            }
        }

        else
        {
            throw new \Exception('Article inconnu.');
        }

    }




}
