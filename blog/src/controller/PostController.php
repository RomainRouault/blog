<?php

namespace Blog\Controller;

use Blog\Model\PostManager;
use Blog\Model\CommentManager;
use Blog\Model\UserManager;
use Blog\Model\Entity\Post;
use Blog\Controller\AuthentificationController;
use Blog\Controller\CommentController;

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

        //call manager and get the posts data
        $postManager = new PostManager();
        $blogPosts = $postManager->getPostsList();

        //if there is no page given, get page 1, else get the page
        !isset($_GET['p']) ? $currentPage = $_GET['p']= 1 : $currentPage = (int)$_GET['p'];

        //get the number of pages to display
        $pagination = $this->pagination($blogPosts, $currentPage);
        
        //if page given is superior to maximum page given, redirect to first page
        if ($_GET['p'] > $pagination['totalPage']) {
            header('Location: /blog');
        }

        //splice the postsdatas according to the calculation above
        $blogPosts = array_splice($blogPosts, $pagination['offset'], $pagination['limit']);

        //call view
        echo $this->twig->render('blog.twig', array('blogPosts' => $blogPosts, 'pagination' => $pagination));
        //forget about the possible messages
        $this->unsetMessage();
    }

    /**
    *Display single article of blog with comments for frontend
    *
    * @return object Twig
    * @throws Exception
    * @throws Twig_Error
    */
    public function blogPost()
    {
        //if id of the post is given
        if (isset($_GET['id'])) {
            $postId = (int)$_GET['id'];
            //call manager and get the post data
            $postManager = new PostManager();
            $blogPost = $postManager->getPost($postId);

            //if blogPost exist
            if ($blogPost) {
                //get the associated comments
                $commentController = new CommentController();
                $comments = $commentController->commentList($postId);

                //call view
                echo $this->twig->render('post_blog.twig', array('blogPost' => $blogPost, 'comments' => $comments));
                //forget about the possible messages
                $this->unsetMessage();
            } else { //if blogPost does not exist
                $this->setMessage('Article inconnu.', 'front-modal');
                header('location: /blog');
            }
        } else { //if post id is not given, redirect to home page
            header('location: /blog');
        }
    }

    /**
    *display view for administrator panel
    *
    * @return object Twig
    */
    public function backBlog()
    {
        //call post manager
        $postManager = new PostManager();
        $postsList = $postManager->getPostsList();

        //call view
        echo $this->twig->render('posts_list.twig', array('postsList' => $postsList));
        $this->unsetMessage();
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
        if (!empty($_POST['postTitle']) && !empty($_POST['postChapo']) && !empty($_POST['postContent']) && !empty($_POST['token']) && !empty($_SESSION['token'])) {
            // Token checking (prevent CRSF attack)
            if ($_SESSION['token'] == $_POST['token']) {
                //call entity "Post"
                $post = new Post($_POST);
                //call manager
                $postManager = new PostManager();

                $affectedlines = $postManager->addPost($post);

                //if submission had failed, throw a message
                if ($affectedlines === false) {
                    $this->setMessage('Erreur : Impossible d\'ajouter l\'article', 'back-modal');
                    header('Location: /blog/administrator/post/newpost');
                } else {
                    header('Location: /blog/administrator/');
                }
            } else { //token dont match, throw a message
                $this->setMessage('Erreur : Impossible d\'ajouter l\'article.', 'back-modal');
                header('location: /blog/administrator/post/newpost');
            }
        } elseif (isset($_GET['addpost'])) { //if a post have been submited, but not fully completed, throw a message
            $this->setMessage('Merci de remplir tout les champs', 'back-modal');
            header('Location: /blog/administrator/post/newpost');
        } else { //if nothing have been submited
            //call view
            echo $this->twig->render('add_post_form.twig');
            //forget about the possible messages
            $this->unsetMessage();
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
        if (isset($_GET['id'])) {
            //get the post id
            $postid = intval($_GET['id']);

            //Retrieve the post data with the id given
            $postManager = new PostManager();
            $postData = $postManager->getPost($postid);

            //if post data find on the DB
            if (!empty($postData['idPost'])) {
                //if the edition form have been submited
                if (!empty($_POST['postTitle']) && !empty($_POST['idPerson'] /* author */) && !empty($_POST['postChapo']) && !empty($_POST['postContent']) && !empty($_POST['token']) && !empty($_SESSION['token'])) {
                    // Token checking (prevent CRSF attack)
                    if ($_SESSION['token'] == $_POST['token']) {
                        $updated_input = new Post($_POST);
                        $edition = $postManager->updatePost($updated_input, $postid, $_POST['idPerson']);

                        //successful edition, throw a message to confirm
                        if ($edition) {
                            $this->setMessage('Article modifié.', 'back-modal');
                            header('Location: /blog/administrator/');
                        } else { //failed edition, throw a message
                            $this->setMessage('Erreur : Impossible de modifier l\'article.', 'back-modal');
                            header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                        }
                    } else { //token dont match, throw a message
                        $this->setMessage('Erreur : Impossible de modifier l\'article.', 'back-modal');
                        header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                    }
                } elseif (isset($_GET['submit'])) { //if a post have been submited, but not fully completed, throw a message
                    $this->setMessage('Merci de remplir tout les champs', 'back-modal');
                    header('Location: /blog/administrator/post/editpost?id='.$postData['idPost']);
                } else { //if nothing have been submited, get the form edition view
                    //get the users list for display them into a select
                    $userManager = new userManager();
                    $userslist = $userManager->getUsersList();
                    //call the view
                    echo $this->twig->render('edition_post_form.twig', array('post' => $postData, 'users' => $userslist));
                    //forget about the possible messages
                    $this->unsetMessage();
                }
            } else { //no post id given, throw a message
                $this->setMessage('Article inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
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
        if ($_SESSION['token'] == $_GET['token']) {
            //get the post id
            $postid = (int)($_GET['id']);
            //get the post status
            $status = (int)($_GET['publication']);

            $postManager = new PostManager();
            $status = $postManager->updatePostStatus($postid, $status);

            if ($status) {
                header('Location: /blog/administrator/');
            } else {
                $this->setMessage('Article inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
            }
        } else { //token dont match, throw a message
            $this->setMessage('Erreur : Impossible de modifier le statut article.', 'back-modal');
            header('location: /blog/administrator/post/editpost?id='.$_GET['id']);
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
        if ($_SESSION['token'] == $_GET['token']) {
            //get the post id
            $postid = intval($_GET['id']);

            // call manager for delete
            $postManager = new PostManager();
            $delete = $postManager->deletePost($postid);

            //successful removal
            if ($delete) {
                $this->setMessage('Artice supprimé.', 'back-modal');
                header('Location: /blog/administrator/');
            } else { //failed removal (unknow id...)
                $this->setMessage('Supression impossible.', 'back-modal');
                header('Location: /blog/administrator/');
            }
        } else { //wrong token, throw a message
            $this->setMessage('Supression impossible.', 'back-modal');
            header('Location: /blog/administrator/');
        }
    }

    /**
    *calculation of the number of page(s) to display for blog pagination
    *
    * @param array($blogPosts), int($currentPage)
    * @return array
    */
    public function pagination($blogPosts, $currentPage)
    {
        //count the total number of posts
        $nbPost = count($blogPosts);
        //choose the number of posts to display per page
        $limit = 3;
        //calculation of the total number of page
        $totalPage = ceil($nbPost/$limit);

        // calcul offset
        $offset = ($currentPage - 1) * $limit;

        //return the data
        return array('totalPage' => $totalPage, 'limit' => $limit, 'offset' => $offset, 'currentPage' => $currentPage);
    }

    /*****************************************************************/
    /*********** functions to display static pages (front) ***********/
    /*****************************************************************/

    /**
    *display the homepage
    *
    */
    public function home()
    {
        echo $this->twig->display('home.twig');
        $this->unsetMessage();
    }

    /**
    *display the portfolio page
    *
    */
    public function portfolio()
    {
        echo $this->twig->display('portfolio.twig');
        $this->unsetMessage();
    }
}
