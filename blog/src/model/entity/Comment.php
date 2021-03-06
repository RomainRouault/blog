<?php

namespace Blog\Model\Entity;

/**
*Comment Entity used by user to manipulate a comment object
*
*/
class Comment extends Entity
{
    protected $idComment;
    protected $commentContent;
    protected $commentStatus;
    protected $commentPseudo;

    //getters//

    public function idComment()
    {
        return $this->id;
    }

    public function commentContent()
    {
        return $this->commentContent;
    }

    public function commentStatus()
    {
        return $this->commentStatus;
    }

    public function commentPseudo()
    {
        return $this->commentPseudo;
    }


    //setters//
    
    public function setCommentContent($commentContent)
    {
        if (is_string($commentContent)) {
            $this->commentContent = $commentContent;
        }
    }

    public function setCommentStatus($commentStatus)
    {
        $this->commentStatus = (int)$commentStatus;
    }

    public function setCommentPseudo($commentPseudo)
    {
        if (is_string($commentPseudo)) {
            $this->commentPseudo = $commentPseudo;
        }
    }
}
