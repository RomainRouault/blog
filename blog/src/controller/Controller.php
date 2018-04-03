<?php

namespace Blog\Controller;

class Controller
{
    protected $twigloader,
    $twig;

    public function __construct()
    {   
    	//load the templates for Twig
        $this->twigloader = new \Twig_Loader_Filesystem(array('src/view', 'src/view/frontend', 'src/view/backend', 'src/view/frontend/form', 'src/view/backend/form'));
        //load the Twig environment!   
        $this->twig = new \Twig_Environment($this->twigloader);
    }





}