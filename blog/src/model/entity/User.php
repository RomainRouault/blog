<?php
/**
*User Entity used by user to manipulate a user object 
*
*/

namespace Blog\Model\Entity; 

class User extends Entity
{

  protected $id,
  			$userPseudo,
  			$userMail,
  			$userPass,
  			$userRole;

	//getters//

	public function userId()
	{
		return $this->id;
	}

	public function userPseudo()
	{
		return $this->userPseudo;
	}

	public function userMail()
	{
		return $this->userMail;
	}

	public function userPass()
	{
		return $this->userPass;
	}

	public function userRole()
	{
		return $this->userRole;
	}

	//setters

	public function setUserPseudo($userPseudo)
	{
		if(is_string($userPseudo))
		{
			//clean all whitespace
			$userPseudo = preg_replace('/\s+/', '', $userPseudo);
			$this->userPseudo = trim($userPseudo);
		}
	}

	public function setUserMail($userMail)
	{
		if(is_string($userMail))
		{
			$this->userMail = trim($userMail);
		}
	}

	public function setUserPass($userPass)
	{
		if(is_string($userPass))
		{
			//clean all whitespace
			$userPass = preg_replace('/\s+/', '', $userPass);
			//hash the password
			$hashUserPass = password_hash($userPass, PASSWORD_DEFAULT);
			$this->userPass = $hashUserPass;
		}
	}

	public function setUserRole($userRole)
	{
		if(is_string($userRole))
		{
			$this->userRole = $userRole;
		}
	}

}