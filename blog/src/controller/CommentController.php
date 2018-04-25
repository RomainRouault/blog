<?php

namespace Blog\Controller;

use Blog\Model\CommentManager;
use Blog\Model\Entity\Comment;
use Blog\Controller\AuthentificationController;

/**
*Class PostController provides methods for all related post features
*
*/
class CommentController extends Controller
{

    /**
    *Display a list of comments for a post
    *
    * @return array
    * @throws Exception
    * @throws Twig_Error
    */
    public function commentList($postId)
    {
        //call manager and get the posts data
        $commentManager = new CommentManager();
        $Comments = $commentManager->getCommentList($postId);

        return $Comments;
    }


    /**
    *Add the comment in DB if the form is submited.
    *make standard check before add to DB.
    *
    * @return Mixed
    */
    public function newComment()
    {
        //first, check the user with recaptcha API (return true if success)
        /*if ($this->recaptcha())
        {*/
        //if post have been submited
        if (!empty($_POST['commentPseudo']) && !empty($_POST['commentContent']) && !empty($_POST['token']) && !empty($_SESSION['token'])) {
            // Token checking (prevent CRSF attack)
            if ($_SESSION['token'] == $_POST['token']) {
                //call entity "Comment"
                $comment = new Comment($_POST);

                //call manager
                $commentManager = new commentManager();

                $affectedlines = $commentManager->addComment($comment, intval($_GET['id']));

                //if submission have failed, throw a message
                if ($affectedlines === false) {
                    $this->setMessage('Erreur : Impossible d\'ajouter le commentaire', 'front-modal');
                    header('Location: /blog/post/blogpost?id='.$_GET['id']);
                    die();
                } else {
                    $this->setMessage('Votre commentaire a été soumis à un administrateur pour validation', 'front-modal');
                    header('Location: /blog/post/blogpost?id='.$_GET['id']);
                    die();
                }
            }
            //token dont match, throw a message
            else {
                $this->setMessage('Erreur : Impossible d\'ajouter le commentaire.', 'front-modal');
                header('location: /blog/post/blogpost?id='.$_GET['id']);
                die();
            }
        }

        //if a comment have been submited, but not fully completed, throw a message
        else {
            $this->setMessage('Merci de remplir tout les champs', 'front-modal');
            header('Location: /blog/post/blogpost?id='.$_GET['id']);
            die();
        }
        /*}

        else
        {
            $this->setMessage('Erreur : Impossible d\'ajouter le commentaire.', 'front-modal');
            header('location: /blog/post/blogpost?id='.$_GET['id']);
            die();
        }*/
    }

    /**
    * Get all the pending comments and display them to manage their status (backend)
    *
    * @return twig object
    */
    public function pendingComments()
    {
        //Get all pendings comments
        $commentManager = new CommentManager();
        $pendingCommentsList = $commentManager->pendingCommentsList();

        //display the view with the data
        echo $this->twig->render('pending_comments.twig', array('comments' => $pendingCommentsList));
        unset($_SESSION['message'], $_SESSION['message_origin']);
    }

    public function commentEditionStatus()
    {
        // Token checking (prevent CRSF attack)
        if ($_SESSION['token'] == $_GET['token']) {
            //get the post id
            $commentid = (int)($_GET['id']);

            $commentManager = new commentManager();
            $status = $commentManager->updateCommentStatus($commentid);

            //if modification is ok
            if ($status) {
                header('Location: /blog/administrator/');
            } else {
                $this->setMessage('Commentaire inconnu.', 'back-modal');
                header('location: /blog/administrator/comment/pendingcomments');
                die();
            }
        }

        //token dont match, throw a message
        else {
            $this->setMessage('Erreur : Impossible de modifier le statut du commentaire.', 'back-modal');
            header('location: /blog/administrator/comment/pendingcomments');
            die();
        }
    }

    /**
    *Delete a pending comment
    *
    * @return bool
    */
    public function deleteComment()
    {
        // Token checking (prevent CRSF attack)
        if ($_SESSION['token'] == $_GET['token']) {
            //get the comment id
            $commentid = intval($_GET['id']);

            $commentManager = new commentManager();

            $delete = $commentManager->deletePost($commentid);

            //successful removal
            if ($delete) {
                $this->setMessage('Commentaire supprimé.', 'back-modal');
                header('Location: /blog/administrator/');
                die();
            }

            //unkown id, throw a message
            else {
                $this->setMessage('Commentaire inconnu.', 'back-modal');
                header('Location: /blog/administrator/');
                die();
            }
        }

        //token dont match, throw a message
        else {
            $this->setMessage('Erreur : Impossible de modifier le statut article.', 'back-modal');
            header('location: /blog/administrator/post/editpost?id='.$postData['idPost']);
            die();
        }
    }
}
