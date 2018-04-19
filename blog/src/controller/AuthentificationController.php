<?php

namespace Blog\Controller;

use Blog\Model\UserManager;
use Blog\Model\Entity\User;

/**
*Class AuthentificationController managed the authentification feature
*
*
*/
class AuthentificationController extends Controller

{   
    /**
    *Fetch user data on DB with $_Post sent data
    *
    * @return object PDO
    * @throws PDO exception
    */
	public function getUser()
	{
		//check with post data if user is registred
		$pendingUserData = new User($_POST);
		$userManager = new UserManager();

        return $userManager->getUser($pendingUserData);
	}


    /**
    *Check the authentification form
    *
    * @return mixed
    * @throws exception
    */
	public function isRegistred()
	{
		//check if the form had been fully completed
		if (!empty($_POST['userMail']) && !empty($_POST['userPass']))
		{
            //check if Post data (mail) match with a db entry
			$pendingUser = $this->getUser();

            //if not match
            if ($pendingUser == false)
            {
                $this->setMessage('Mot de passe inconnu ou/et email inconnu.', 'auth');
                header('Location:'.$_SERVER['PHP_SELF']);
                die();
            }

            //if mail check is ok, check the password
            else
            {
                $pendingPass = $_POST['userPass'];
                if(password_verify($pendingPass, $pendingUser['personPass']))
                {
                    //if pass is ok too, create session attr
                	$this->setAuthentificated($pendingUser);

                    //if the authentificated user is an admin, redirect 
                    if ($pendingUser['personRole'] == 'admin')
                    {
                    	header('Location: ../administrator/');
                    	die();
                    }

                    //if is a standard user, stay on front
                    else
                    {
                        header('Location:'.$_SERVER['PHP_SELF']);
                        die();
                    }
            	}

                else
                {
                    $this->setMessage('Mot de passe inconnu ou/et email inconnu.', 'auth');
                    header('Location:'.$_SERVER['PHP_SELF']);
                    die();
                }           
            }
        }

		else
		{
			$this->setMessage('Tout les champs doivent être remplis', 'auth');
            header('Location:'.$_SERVER['PHP_SELF']);
            die();
		}

	}

    /**
    *Add a user via the registration form
    *
    * @return mixed
    * @throws exception
    */
	public function addUser()
	{
		//check if the form had been fully completed
		if (!empty($_POST['userPseudo']) && !empty($_POST['userMail']) && !empty($_POST['userPass']))
		{
            //instantiate a new user object for DB submission
            $newUser = new User($_POST);

        	$userManager = new UserManager();
            $addUserOk = $userManager->addUser($newUser);

            // if the user is added
            if ($addUserOk)
            {
                $user = $this->getUser();
            	$this->setAuthentificated($user);
            	header('Location:'.$_SERVER['PHP_SELF']);
                die();
            }

            //if adding failed (mail aldready registred on db (unique index on db))
            else
            {
            	$this->setMessage('Email déjà enregistré.', 'reg');
                header('Location:'.$_SERVER['PHP_SELF']);
                die();
            }
        }  
	}

    /**
    *Assign user values to the global var session. 
    *Called by adduser() or isregistred() method.
    *
    * @return var
    */
	public function setAuthentificated($user)
	{	 
        $_SESSION['pseudo'] = $user['personPseudo'];
        $_SESSION['mail'] = $user['personMail'];
        $_SESSION['role'] = $user['personRole'];
	}

    /**
    *destroy the session. 
    *Called in back or front.
    *
    */
    public function logout()
    {
        session_destroy();
        header('Location: /blog');
    }

}