<?php
/**
*Class Router finds the appropriate method for the request
*
*Instantiated first by the application in the index.php file.  
*/

namespace Blog\Controller\Router;

use Blog\Controller\PostController;

Class Router
{
	protected $paths;
	protected $queries;

	/**
	*
	*
	* @param array $request The URI request  
	*/
	public function __construct($request)
	{
		//parse URI
		$parsedRequest = parse_url($request);

		//call method to set the paths attribute
		$this->setPaths($parsedRequest);

		//if present, call method to set the queries attribute
		if (isset($parsedRequest['query']))
		{
		$this->setQueries($parsedRequest);
		}

		//call method to get the right controller
		$this->getController();
	}

	public function setPaths($parsedRequest)
	{
		//explode paths in request
		$paths = explode('/', $parsedRequest['path']);

		//check array and set paths attribute
		foreach ($paths as $path)
		{
			if (is_string($path))
			{
				$this->paths = $paths;
			}
		}

	}

	public function setQueries($parsedRequest)
	{
		//explode queries in request
		$queries = explode('?', $parsedRequest['query']);

		//check array and set paths attribute
		foreach ($queries as $querie)
		{
			if (is_string($querie))
			{
				$this->queries = $queries;
			}
		}

	}


	public function getController()
	{

		if ($this->paths['2'] == 'administrator')
		{
			if	(isset($this->paths['3']) == 'newpost' && isset($this->queries['0']) == 'addpost' )
			{
				//if a new post is submit
				$PostController = new PostController();
				$PostController->addPost();
			}

			elseif ($this->paths['3'] == 'newpost')
			{
				//display the form for adding post
				$PostController = new PostController();
				$PostController->addPostForm();

			}

			else
			{
				//display the default admin panel
				$PostController = new PostController();
				$PostController->postsList();
			}
		}

		else
		{
			//default front-end page
			$PostController = new PostController();
			$PostController->blog();
		}
	}

}

