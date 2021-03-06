<?php

namespace Blog\Controller\Router;

use Blog\Controller\PostController;
use Blog\Controller\CommentController;
use Blog\Controller\AuthentificationController;

/**
*Class Router finds the appropriate method for the request via paths from URI
*
*Instantiated first by the application in the index.php file.
*/
class Router
{
    protected $paths;

    /**
    * parse the URI request for extract the paths
    * Call first method 'backOrFront' to initiate the research for the appropriate method
    *
    * @param array $request The URI request
    */
    public function __construct($request)
    {
        
        //parse URI
        $parsedRequest = parse_url($request);
        //call method to set the paths attribute
        $this->setPaths($parsedRequest);

        //call method to trigger the process for find the right controller/method
        $this->backOrFront();
    }

    /**
    * Assign value to paths attribute
    *
    * @var
    */
    public function setPaths($parsedRequest)
    {
        //explode paths in request
        $paths = explode('/', $parsedRequest['path']);

        //filter array and set paths attribute
        $paths = array_filter($paths, 'is_string');
        $paths = array_filter($paths);
        
        //set attribute
        $this->paths = $paths;
    }

    /**
    * Find witch method to call (different behavior if front or back)
    * Check the user role for administration acess via the global session
    *
    */
    public function backOrFront()
    {
        if (isset($this->paths['2'])) {
            if ($this->paths['2'] == 'administrator') {
                //get controller for the backend
                //check if the user is an admin
                if ($_SESSION['role'] == 'admin') {
                    $this->getControllerMethodBack();
                } else { //not an admin
                    header('Location:'. $_SERVER['PHP_SELF']);
                }
            } else { //we are on the frontend
                $this->getControllerMethodFront();
            }
        } else {
            //the controller for the front end
            $this->getControllerMethodFront();
        }
    }

    /**
    * Automatic assignment of the controller and is associated method via the paths for the backend
    *
    * Memo for the paths values :
    *   path[1] = 'blog';
    *   path[2] = 'administrator'(back);
    *   path[3] = controller;
    *   path[4] = method;
    */
    public function getControllerMethodBack()
    {
        //if there is a fourth path
        if (isset($this->paths['4'])) {
            //controller/path parsing
            $controller = $this->paths['3'];
            $parsedController = ucfirst($controller).'Controller';
            $controller = 'Blog\\Controller\\'.$parsedController;

            //method = fourth path
            $method = $this->paths['4'];

            //test if the method/path exist
            if (method_exists($controller, $method)) {
                //instantiate controller
                $controller = new $controller();
                //call method
                $controller->$method();
            } else { //if method/path is unknow, display the main backend page
                $PostController = new PostController();
                //default main page
                $PostController->backBlog();
            }
        } else { //if there is no fourth path, we want the main page
            $PostController = new PostController();
            //default backend page
            $PostController->backBlog();
        }
    }
    /**
    * Automatic assignment of the controller and is associated method via the paths for the frontend
    *
    * Memo for the paths values :
    *   path[1] = 'blog';
    *   path[2] = controller or 'assets';
    *   path[3] = method;
    */
    public function getControllerMethodFront()
    {
        //if there is a third path
        if (isset($this->paths['3'])) {
            //controller/path parsing
            $controller = $this->paths['2'];
            $parsedController = ucfirst($controller).'Controller';
            $controller = 'Blog\\Controller\\'.$parsedController;

            //method = third path
            $method = $this->paths['3'];

            //test if the method/path exist
            if (method_exists($controller, $method)) {
                //instantiate controller
                $controller = new $controller();
                //call method
                $controller->$method();
            }
            else { // if method/path does not exist
                $PostController = new PostController();
                //default main page
                $PostController->home();
            }
        } else { //if there is no third path, we want the main page
            $PostController = new PostController();
            //default main page
            $PostController->home();
        }
    }
}
