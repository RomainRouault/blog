<?php

namespace Blog\Model;

/**
*Class used to communicate with database for getting post data
*
*/
class PostManager extends Manager
{
    /**
    * Get the list of all posts
    * @return array
    * @throws PDOException
    */
    public function getPostsList()
    {
        $db = $this->dbConnect();

        $req = $db->query('SELECT idPost, postTitle, postChapo, postUpdate, postStatus, post.idPerson, personPseudo, personRole FROM post JOIN person ON post.idPerson = person.idPerson ORDER BY postCreation DESC');
        $postslist = $req->fetchAll();
        return $postslist;
    }

    /**
    * Get a post data
    * @param int $idPost
    * @return array
    * @throws PDOException
    */
    public function getPost($idPost)
    {
        $db = $this->dbConnect();

        $req = $db->prepare('SELECT idPost, postTitle, postChapo, postContent, postCreation, postUpdate, postStatus, post.idPerson, personPseudo, personRole FROM post JOIN person ON post.idPerson = person.idPerson WHERE idPost = ?');
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
    public function updatePost($updated_input, $postid, $idPerson)
    {
        $db = $this->dbConnect();

        $req = $db->prepare('UPDATE post SET postTitle= :title, postChapo= :chapo, postContent=  :content, postUpdate= NOW(), postStatus= :status, idPerson= :idPostPerson WHERE idPost = :id');
        $req->bindValue(':id', $postid);
        $req->bindValue(':title', $updated_input->postTitle());
        $req->bindValue(':chapo', $updated_input->postChapo());
        $req->bindValue(':content', $updated_input->postContent());
        $req->bindValue(':status', $updated_input->postStatus());
        $req->bindValue(':idPostPerson', $idPerson);
        $affectedLines = $req->execute();

        return $affectedLines;
    }

    /**
    * Update the status of a post (edition)
    * @return object PDO
    * @throws PDOException
    */
    public function updatePostStatus($postid, $status)
    {
        $db = $this->dbConnect();

        $req = $db->prepare('UPDATE post SET postStatus=? WHERE idPost = ?');
        $affectedLines = $req->execute(array($status, $postid));

        return $affectedLines;
    }

    /**
    *Delete a post
    * @return bool
    * @throws PDOException
    */
    public function deletePost($idPost)
    {
        $db = $this->dbConnect();

        $req = $db->prepare('DELETE FROM post WHERE idPost = ?');
        $delete = $req->execute(array($idPost));

        return $delete;
    }
}
