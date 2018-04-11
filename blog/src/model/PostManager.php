<?php
/**
*Class used to communicate with database 
*
*/

namespace Blog\Model;

use Blog\Model\Post;

class PostManager extends Manager
{

    /**
    * Get the list of the published posts
    * @return object PDO
    * @throws PDOException
    */
    public function getPublishedPosts()
    {
        $db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr FROM post WHERE postStatus = 1 ORDER BY postCreation DESC');

        return $req;
    }

    /**
    * Get the list of all posts
    * @return object PDO
    * @throws PDOException
    */
    public function getAllPostsList()
    {
        $db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr, postStatus FROM post ORDER BY postCreation DESC');

        return $req;
    }

    /**
    * Get a post data
    * @param int $idPost
    * @return object PDO 
    * @throws PDOException
    */
    public function getPost($idPost)
    {
        $db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, postContent, postTag, DATE_FORMAT(postCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS creation_date_fr, DATE_FORMAT(postUpdate, \'%d/%m/%Y à %Hh%imin%ss\') AS update_date_fr, postStatus FROM post WHERE idPost = ?');
        $req->execute(array($idPost));
        $post = $req->fetch();

        return $post;
    }

    /**
    * Add a new post
    * @return object PDO 
    * @throws PDOException
    */
    public function addPost($post)
    {
        $db = $this->dbConnect();

        $req = $db->prepare('INSERT INTO post (postTitle, postChapo, postContent, postCreation, postUpdate, postStatus, idPerson) VALUES (:title, :chapo, :content, NOW(), NOW(), 1, 1)');
        $req->bindValue(':title', $post->postTitle());
        $req->bindValue(':chapo', $post->postChapo());
        $req->bindValue(':content', $post->postContent());
        $affectedLines = $req->execute();

        return $affectedLines;
    }

    /**
    * Update a post (edition)
    * @return object PDO 
    * @throws PDOException
    */
    public function updatePost()
    {
    }

    /**
    *Update the status of a post(published or not published)
    * @return object PDO 
    * @throws PDOException
    */
    public function updatePostStatus()
    {
    }

    /**
    *Delete a post
    * @return bool
    * @throws PDOException
    */
    public function deletePost()
    {
    }

}
