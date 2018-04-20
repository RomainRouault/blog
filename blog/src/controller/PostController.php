<?php

namespace Blog\Controller;

use Blog\Model\PostManager;
use Blog\Model\Entity\Post;
use Blog\Controller\AuthentificationController;

/**
*Class PostController provides methods for all related post features
*
*/
class PostController extends Controller
{   

    /**
    *Display published article in blog for frontend
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
                $this->setMessage('Il n\'y a pas encore d\'article publié', 'back-modal');
                header('Location:'.$_SERVER['PHP_SELF'].'/administrator/');
                die();
            }

            //call view
            echo $this->twig->render('blog_view.twig', array('blogPosts' => $blogPosts));
            //forget about the possible messages 
            unset($_SESSION['message'], $_SESSION['message_origin']);
    }

    /**
    *display view for administrator panel
    *
    * @return object Twig
    */
    public function backBlog()
    {
        //call manager
       $postManager = new PostManager();
       $postsList = $postManager->getPostsList();

        //call view
        echo $this->twig->render('posts_list.twig', array('postsList' => $postsList));
        unset($_SESSION['message'], $_SESSION['message_origin']);
    }


    /**
    *display form for adding post in backend. Add the post in DB if the form is submited.
    *make standard check before add to DB.
    *
    * @return Mixed
    */
    public function newPost()
    {
        //if post have been submited
        if (!empty($_POST['postTitle']) && !empty($_POST['postChapo']) && !empty($_POST['postContent']) && !empty($_POST['token']) && !empty($_SESSION['token']))
        {
            // Token checking (prevent CRSF attack)
            if ($_SESSION['token'] == $_POST['token']) 
            {

                $post = new Post($_POST);
                $postManager = new PostManager();

                $affectedlines = $postManager->addPost($post);

                //if submission had failed, throw a message
                if ($affectedlines === false) 
                {
                    $this->setMessage('Erreur : Impossible d\'ajouter l\'article', 'back-modal');
                    header('Location: /blog/administrator/post/newpost');
                    die();
                }

                else 
                {
                    header('Location: /blog/administrator/');
                }
            }
            //token dont match, throw a message
            else
            {
                $this->setMessage('Erreur : Impossible d\'ajouter l\'article.', 'back-modal');
                header('location: /blog/administrator/post/newpost');
                die();
            }

        }

        //if a post have been submited, but not fully completed, throw a message
        elseif (isset($_GET['addpost']))
        {
                $this->setMessage('Merci de remplir tout les champs', 'back-modal');
                header('Location: /blog/administrator/post/newpost');
                die();        
        }

        //if nothing have been submited
        else
        {
        //call view
        echo $this->twig->render('add_post_form.twig');
        //forget about the possible messages 
        unset($_SESSION['message'], $_SESSION['message_origin']);
        }
    }

    /**
    *display form for edit post in backend. Update the post in DB if the form is submited.
    *make standard check before update in DB.
    * 
    * @return mixed
    */
    public function editPost()
    {
        //check if a post id given
        if (isset($_GET['id']))
        {
            //get the post id
            $postid = intval($_GET['id']); 

            //Retrieve the post data with the id given
            $postManager = new PostManager();
            $postData = $postManager->getPost($postid);

            //if post data find on the DB
            if (!empty($postData['idPost']))
            {
                //if the edition form have been submited
                if (!empty($_POST['postTitle']) && !empty($_POST['postChapo']) && !empty($_POST['postContent']) && !empty($_POST['token']) && !empty($_SESSION['token']))
                {

                    // Token checking (prevent CRSF attack)
                    if ($_SESSION['token'] == $_POST['token']) 
                    {
                        $updated_input = new Post($_POST);
                        $edition = $postManager->updatePost($updated_input, $postid);

                        //successful edition, throw a message to confirm
                        if ($edition)
                        {
                            $this->setMessage('Article modifié.', 'back-modal');
                            header('Location: /blog/administrator/');
                            die();
                        }

                        //failed edition, throw a message
                        else
                        {
                            $this->setMessage('Erreur : Impossible de modifier l\'article.', 'back-modal');
                            header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                            die();
                        }
                    }

                    //token dont match, throw a message
                    else
                    {
                        $this->setMessage('Erreur : Impossible de modifier l\'article.', 'back-modal');
                        header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                        die();
                    }

                }

                //if a post have been submited, but not fully completed, throw a message
                elseif (isset($_GET['submit']))
                {
                        $this->setMessage('Merci de remplir tout les champs', 'back-modal');
                        header('Location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                        die();        
                }

                //if nothing have been submited, display the form edition view
                else
                {
                    echo $this->twig->render('edition_post_form.twig', array('post' => $postData));
                    //forget about the possible messages 
                    unset($_SESSION['message'], $_SESSION['message_origin']);
                }

            }

            //no post id given, throw a message
            else
            {
                $this->setMessage('Article inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
                die();
            }
        }
    }

    /**
    *Edit the status of a post (published or not)
    * 
    * @return bool
    */
    public function postEditionStatus()
    {

        // Token checking (prevent CRSF attack)
        if ($_SESSION['token'] == $_GET['token']) 
        {
            //get the post id
            $postid = (int)($_GET['id']); 
            //get the post status
            $status = (int)($_GET['publication']);

            $postManager = new PostManager();
            $status = $postManager->updatePostStatus($postid, $status);

            if ($status)
            {
                header('Location: /blog/administrator/');
            }

            else
            {
                $this->setMessage('Article inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
                die();
            }
        }

        //token dont match, throw a message
        else
        {
            $this->setMessage('Erreur : Impossible de modifier le statut article.', 'back-modal');
            header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
            die();
        }

    }

    /**
    *Delete a post
    * 
    * @return bool
    */
    public function deletePost()
    {
        // Token checking (prevent CRSF attack)
        if ($_SESSION['token'] == $_GET['token']) 
        { 
            //get the post id
            $postid = intval($_GET['id']);

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
                    header('Location: /blog/administrator/');
                }
            }

            //unkown id, throw a message
            else
            {
                $this->setMessage('Article inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
                die();
            }
        }

        //token dont match, throw a message
        else
        {
            $this->setMessage('Erreur : Impossible de modifier le statut article.', 'back-modal');
            header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
            die();
        }
    }

}
