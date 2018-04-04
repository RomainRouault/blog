<?php

namespace Blog\Controller\Router;

use Blog\Controller\PostController;

Class Router
{

	public function __construct($request)
	{
		if ($request == 'addpost')
		{
			$PostController = new PostController();
			echo $PostController->addPostForm();
		}

		else
		{
			//default front-end page
			$FrontEndController = new PostController();
			echo $FrontEndController->blog();
		}
	}

}

