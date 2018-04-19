<?php
/**
*Class Router finds the appropriate method for the request
*
*Instantiated first by the application in the index.php file.  
*/

namespace Blog\Controller\Router;

use Blog\Controller\PostController;
use Blog\Controller\AuthentificationController;

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
		//get controller for the backend
		{
			if (isset($_SESSION))
			//get the good controller only if user is authentified
			{
				//instantiate the Controllers objects;
				$PostController = new PostController();
				$AuthentController = new AuthentificationController();

				if ($_SESSION['auth'] && $_SESSION['role'] == 'admin')
				{

					if	(isset($this->paths['3']) && isset($this->queries['0']))
					//if there at least one query
					{
						if ($this->queries['0'] == 'addpost')
						{
							//if a new post is submit
							$PostController->addPost();
						}

						elseif ($this->paths['3'] == 'delete' && (isset($_GET['id'])))
						{
							//ask for delete - get the post id
							$postid = intval($_GET['id']);
							$PostController->deletePost($postid);
						}

						/************Post Edition************/
						elseif ($this->paths['3'] == 'editpost' && (isset($_GET['id'])))
						{
							$postid = intval($_GET['id']);

							if (isset($_GET['submit']))
							{
								//if a post edition is submited
								$submit = true;
								$PostController->postEdition($postid, $submit);
							}

							elseif (isset($_GET['publication']))
							{
								//if the publication button had been clicked
								$status = (int)$_GET['publication'];
								$PostController->postEditionStatus($postid, $status);
							}

							else
							{
								//display the form for post edition
								$submit = false;
								$PostController->postEdition($postid, $submit);
							}
						}

					}


					elseif ($this->paths['3'] == 'newpost')
					{
						//display the form for adding post
						$PostController->addPostForm();
					}

					elseif ($this->paths['3'] == 'logout')
					{
						//session destroyed
						$AuthentController->logout();
					}

					else
					{
						//display the default admin panel
						$PostController->postsList();
					}
				}

				else //not authentified
				{
					header('Location:'. $_SERVER['PHP_SELF']);
					die();
				}
			}

			else //no session
			{
				echo "pas de session";
				header('Location:'. $_SERVER['PHP_SELF']);
				die();
			}
		}

		else
		//the controller for the front end
		{
			if ($this->paths['2'] == 'auth' && (isset($this->queries['0'])))
			{
				if ($this->queries['0'] == 'auth')
				{
					//if a new connection is submited
					$AuthentController = new AuthentificationController();
					$AuthentController->isRegistred();
				}

				elseif ($this->queries['0'] == 'reg')
				{
					$AuthentController = new AuthentificationController();
					$AuthentController->addUser();				
				}

				else
				{
					header('Location:'.$_SERVER['PHP_SELF']);
					die();
				}
			}

			elseif ($this->paths['2'] == 'logout')
			{
				//session destroyed
				$AuthentController = new AuthentificationController();
				$AuthentController->logout();
			}

			else
			{
				//default front-end page
				$PostController = new PostController();
				$PostController->blog();
			}

		}
	}
}

