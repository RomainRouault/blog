<?php
/**
*Class used to communicate with database 
*
*/

namespace Blog\Model;

class UserManager extends Manager
{

	public function getUser($pendingUserData)
	{
		$db = $this->dbConnect();

                $req = $db->prepare('SELECT idPerson, personPseudo, personMail, personPass, personRole FROM person WHERE personMail = ?');
                $req->execute(array($pendingUserData->userMail()));
                $user = $req->fetch();

                return $user;	
        }

	public function addUser($pendingUserData)
	{
		$db = $this->dbConnect();
		 $req = $db->prepare('INSERT INTO person (personPseudo, personMail, personPass, personRole) VALUES (:pseudo, :mail, :pass, :role)');
                $req->bindValue(':pseudo', $pendingUserData->userPseudo());
                $req->bindValue(':mail', $pendingUserData->userMail());
                $req->bindValue(':pass', $pendingUserData->userPass());
                $req->bindValue(':role', 'registred');
                $affectedLines = $req->execute();

                return $affectedLines;
	}

}