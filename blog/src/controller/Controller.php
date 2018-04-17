<?php

namespace Blog\Controller;

/**
*Abstract Class Controller instantiates the twig environement used for generate views
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
            return sprintf('../assets/%s', ltrim($asset, '/'));
        }));
        //add session as a global in the Twig environment
        $this->twig->addGlobal('session', $_SESSION);
    }





}