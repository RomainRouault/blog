<?php
/**
*Post Entity used by user to manipulate a post object 
*
*/

namespace Blog\Model\Entity; 

class Post extends Entity
{
  protected $id,
            $postTitle,
            $postChapo,
            $postContent,
            $postTag,
            $postCreation,
            $postUpdate,
            $postStatus;

	//getters//

	public function postId()
	{
	return $this->id;
	}

	public function postTitle()
	{
	return $this->postTitle;
	}

	public function postChapo()
	{
	return $this->postChapo;
	}

	public function postContent()
	{
	return $this->postContent;
	}

	public function postTag()
	{
	return $this->postTag;
	}

	public function postCreation()
	{
	return $this->postCreation;
	}

	public function postUpdate()
	{
	return $this->postUpdate;
	}

	public function postStatus()
	{
	return $this->postStatus;
	}

	//setters//
	
	public function setPostTitle($postTitle)
	{
		if(is_string($postTitle))
		{
			$this->postTitle = $postTitle;
		}
	}

	public function setPostChapo($postChapo)
	{
		if(is_string($postChapo))
		{
			$this->postChapo = $postChapo;
		}
	}

	public function setPostContent($postContent)
	{
		if(is_string($postContent))
		{
			$this->postContent = $postContent;
		}
	}

	public function setPostTag($postTag)
	{
		if(is_string($postTag))
		{
			$this->postTag = $postTag;
		}
	}

	public function setPostCreation(\DateTime $postCreation)
	{
			$this->postCreation = $postCreation;
	}

	public function setPostUpdate(\DateTime $postUpdate)
	{
			$this->postUpdate = $postUpdate;
	}

	public function setPostStatus($postStatus)
	{
			$postStatusInt = (int)$postStatus;
			$this->postStatus = $postStatusInt;
	}

}