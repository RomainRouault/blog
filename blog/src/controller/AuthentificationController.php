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

	public function getUser()
	{
		//check with mail if user is registred
		$pendingUserData = new User($_POST);
		$userManager = new UserManager();

        return $userManager->getUser($pendingUserData);
	}

	public function isRegistred()
	{
		//check if the form had been fully completed
		if (!empty($_POST['userMail']) && !empty($_POST['userPass']))
		{
			$pendingUser = $this->getUser();

            if ($pendingUser === null)
            {
                $this->setMessage("Mot de passe inconnu");
            }

            //check if pass is ok
            else
            {
            	$pendingPass = $_POST['userPass'];

                if(password_verify($pendingPass, $pendingUser['personPass']))
                {
                	$this->setAuthenticated($pendingUser);

                    //if the authentificated user is an admin, redirect 
                    if ($pendingUser['personRole'] == 'admin')
                    {
                    	header('Location: administrator/');
                    	die();
                    }

                    //If is a standard user, stay on front
                    else
                    {
                        header('Location:'.$_SERVER['PHP_SELF']);
                        die();
                    }
            	}

            	else
            	{
            		$this->setMessage("Mot de passe inconnu");
                    header('Location:'.$_SERVER['PHP_SELF']);
                    die();
            	}
            }

		}

		else
		{
			$this->setMessage('Tout les champs doivent être remplis');
            header('Location:'.$_SERVER['PHP_SELF']);
            die();

		}

	}

	public function addUser()
	{
		//check if the form had been fully completed
		if (!empty($_POST['userPseudo']) && !empty($_POST['userMail']) && !empty($_POST['userPass']))
		{
			//check if user is already registred
			$pendingUserData = $this->getUser();

			//if there is no match, add to DB
            if ($pendingUserData === false)
            {
	            //instantiate a new user object for DB submission
	            $newUser = new User($_POST);

            	$userManager = new UserManager();
                $addUserOk = $userManager->addUser($newUser);

                // if the user is added
                if ($addUserOk)
                {
                    $user = $this->getUser();
                	$this->setAuthenticated($user);
                	header('Location:'.$_SERVER['PHP_SELF']);
                    die();
                }

                //if adding fail
                else
                {
                	$this->setMessage("Ajout impossible");
                    header('Location:'.$_SERVER['PHP_SELF']);
                    die();

                
                }
            }

            //mail already registred
            else
            {
            	$this->setMessage("Mail déjà utilisé.");
                header('Location:'.$_SERVER['PHP_SELF']);
                die();
            }

	        
		}
	}

	public function setAuthenticated($user)
	{	 
		$_SESSION['auth'] = true;
        $_SESSION['pseudo'] = $user['personPseudo'];
        $_SESSION['mail'] = $user['personMail'];
        $_SESSION['role'] = $user['personRole'];
	}

    public function setMessage($message)
    { 
        $_SESSION['message'] = $message;

        //find where the message argument come from
        $backtrace = debug_backtrace();

        if (isset($backtrace[1]['function']) && $backtrace[1]['function'] == 'isRegistred')
        {
            $_SESSION['message_origin'] = 'auth';
        }

        else 
        {
            $_SESSION['message_origin'] = 'reg';            
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /blog');
    }

}