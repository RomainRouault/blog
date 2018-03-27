<?php

namespace Blog\Controller; 

use Blog\Model\PostManager;

class FrontEndController()
{

	function blog()
	{
		$postManager = new PostManager();
		$blogPosts = $postManager->getPublishedPosts();

		if (empty($blogPosts)){
			throw new Exception('Il n\'y a pas encore d\'article publié');
		}

		require('./view/front/blogView.html.twig');
	}

}