<?php

namespace Blog\Controller;

/**
*Abstract Class Controller instantiates the twig environement, manage message and recaptcha API
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
    * The twig environement for instantiates twig objects
    * @var Twig_Environment
    */
    protected $twig;

    public function __construct()
    {   
    	//load the templates for Twig
        $this->twigloader = new \Twig_Loader_Filesystem(array('src/view', 'src/view/frontend', 'src/view/backend', 'src/view/frontend/form', 'src/view/backend/form'));
        //load the Twig environment
        $this->twig = new \Twig_Environment($this->twigloader);
        //add function "asset"
        $this->twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset)
        {
            return sprintf('/blog/assets/%s', ltrim($asset, '/'));
        }));
        //add session as a global in the Twig environment
        $this->twig->addGlobal('session', $_SESSION);
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

        if ($decode['success'] == true) 
        {
            return true;
        }
    }



}