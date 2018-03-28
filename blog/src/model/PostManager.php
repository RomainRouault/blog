<?php

namespace Blog\Model;

use Blog\Model\Manager;
use Blog\Entity\Post;

class PostManager extends Manager
{

	//Get the list of the published posts
	public function getPublishedPosts()
	{

		$db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr FROM post WHERE postStatus = 1 ORDER BY postCreation DESC');

        return $req;
	}

	//Get the list of all posts
	public function getAllPostsList()
	{

		$db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr,postStatus postStatus FROM post ORDER BY postCreation DESC');

        return $req;
	}

	//Get a post data
	public function getPost($idPost)
	{

		$db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, postContent, postTag, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr, DATE_FORMAT(postUpdate, \'%d/%m/%Y à %Hh%imin%ss\') AS update_date_fr, postStatus FROM post WHERE idPost = ?');
        $req->execute(array($idPost));
        $post = $req->fetch();

        return $post;
	}

	//Add a new post
	public function addPost()
	{

	}

	//Update a post (edition)
	public function updatePost()
	{

	}

	//Update the status of a post(published or not published)
	public function updatePostStatus()
	{

	}

	//Delete a post
	public function deletePost()
	{

	}
}