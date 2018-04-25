<?php

namespace Blog\Controller;

use Blog\Model\CommentManager;

/**
*Abstract Class Controller instantiates the twig environement, manage session and recaptcha API
*
*/
abstract class Controller
{
    /**
    * Store the templates directories
    * @var array
    */
    protected $twigloader;

    /**
    * The twig environement for instantiates twig objects and token generation for session
    * @var Twig_Environment
    */
    protected $twig;

    public function __construct()
    {
        //load the templates for Twig
        $this->twigloader = new \Twig_Loader_Filesystem(array('src/view', 'src/view/frontend', 'src/view/backend', 'src/view/frontend/form', 'src/view/backend/form'));
        //load the Twig environment
        $this->twig = new \Twig_Environment($this->twigloader, array('debug' => true));

        $this->twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
            return sprintf('/blog/assets/%s', ltrim($asset, '/'));
        }));

        //add a function to twig for count pending comments
        $this->pendingCommentsCounter();

        //add session as a global in the Twig environment
        $this->twig->addGlobal('session', $_SESSION);

        //add extension for debug
        $this->twig->addExtension(new \Twig_Extension_Debug());

        //generate token for session
        if (empty($_SESSION['token'])) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['token'] = $token;
        }
    }


    /**
    *Set a message to display via the global session
    *
    * @return string
    */
    public function setMessage($message, $origin)
    {
        $_SESSION['message'] = $message;
        $_SESSION['message_origin'] = $origin;
    }

    /**
    * Unset the message displayed via the global session. To call after calling a view.
    *
    * @return bool
    */
    public function unsetMessage()
    {
        unset($_SESSION['message'], $_SESSION['message_origin']);
    }

    /**
    * Recaptcha API : checking the user
    *
    * @return bool
    */
    public function recaptcha()
    {
        //captcha secret
        $secret = "6LcXL0EUAAAAAPe1yBSEp3pL1JsIQgjQ6b4YN9y8";
        // params return by the recaptcha
        $response = $_POST['g-recaptcha-response'];
        // User IP
        $remoteip = $_SERVER['REMOTE_ADDR'];

        // sent data to google
        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response."&remoteip=".$remoteip ;

        // decode the json file returned
        $decode = json_decode(file_get_contents($api_url), true);

        if ($decode['success'] === true) {
            return true;
        }
    }

    /**
    *A counter of pending comments for backend
    *
    *Add a function to twig environnement for display counter on every page of the template
    *
    * @return float
    */
    public function pendingCommentsCounter()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('pendingCommentsCounter', function () {
            $commentManager = new CommentManager();
            $pendingCommentsList = $commentManager->pendingCommentsList();
            $pendingCommentsCount = count($pendingCommentsList);

            return $pendingCommentsCount;
        }));
    }
}
