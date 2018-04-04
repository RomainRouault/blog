<?php

require_once __DIR__ . '/vendor/autoload.php';

use Blog\Controller\Router\Router;
use Blog\Controller\PostController;
sftp://u91990180@home718524582.1and1-data.host/blog/vendor/autoload.php
try
{	
	if(isset($_GET['r']))
	{
		//if there is a request. Router is called.
		$request = $_GET['r'];
		$router = new Router($request);
	}

	else
	{
		//Default page
		$PostController = new PostController();
		return $PostController->blog();
	}
}

//gestion des erreurs
catch (PDOException $e)
{
	  echo 'La connexion a échoué.<br />';
	  echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); 
}

catch (Twig_Error $e)
{
	echo 'Erreur Twig :' . $e->getMessage();
}

catch(Exception $e)
{
    echo 'Erreur : ' . $e->getMessage();
}