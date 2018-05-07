<?php

namespace Blog\Controller;

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Blog\Config\Config;

/**
*Class ContactController managed the contact feature
*
*/
class ContactController extends Controller
{
    /**
    * Function for managed the contact form
    *
    * If there is data posted, instanciate the PHPmailer object, else display the form view
    *
    */
    public function contactForm()
    {
        //check if a from have been sent
        if (!empty($_POST['contactMail']) && !empty($_POST['contactName']) && !empty($_POST['contactSubject']) && !empty($_POST['contactMessage']) && !empty($_POST['token']) && !empty($_SESSION['token'])) {
            //first, check the user with recaptcha API (return true if success)
            if ($this->recaptcha()) {
                if ($_SESSION['token'] == $_POST['token']) {
                    //Create a new PHPMailer instance
                    $mail = new PHPMailer();
                    //Tell PHPMailer to use SMTP - requires a local mail server
                    //Faster and safer than using mail()
                    $mail->isSMTP();
                    $mail->SMTPDebug = 0;
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $mail->Host = "smtp.1and1.com";
                    $mail->Port = 25;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tsl';
                    $mail->Username = Config::EMAIL_USERNAME;
                    $mail->Password = Config::EMAIL_PASSWORD;

                    //Set mail adress
                    $mail->setFrom('contact@romain-rouault.fr', $_POST['contactName']).' ('.$_POST['contactMail'].')';
                    //Send the message to yourself, or whoever should receive contact for submissions
                    $mail->addAddress('contact@romain-rouault.fr', 'Romain Rouault');

                    //Put the submitter's address in a reply-to header
                    //This will fail if the address provided is invalid,
                    //in which case we should ignore the whole request
                    if ($mail->addReplyTo($_POST['contactMail'], $_POST['contactName'])) {
                        $mail->Subject = 'Formulaire de contact - Romain Rouault';
                        //use HTML
                        $mail->isHTML(true);
                        //Subject of mail
                        $mail->Subject = $_POST['contactSubject'];
                        //Build a simple message body
                        $mail->Body = 'Un nouveau message de <strong>'.$_POST['contactName'].'</strong> ('.$_POST['contactMail'].') :<br/>' .$_POST['contactMessage'];
                        $mail->AltBody = $_POST['contactMessage'];
                        //Send the message, check for errors
                        if (!$mail->send()) {
                            //The reason for failing to send will be in $mail->ErrorInfo
                            //but you shouldn't display errors to users - process the error, log it on your server.
                            $this->setMessage('Erreur : Impossible d\'envoyer le message' . $mail->ErrorInfo, 'front-modal');
                            header('Location: /blog#contact');
                        } else {//success
                            $this->setMessage('Message envoyé!', 'front-modal');
                            header('Location: /blog#contact');
                        }
                    } else {
                        $this->setMessage('Erreur : adresse email invalide', 'front-modal');
                        header('Location: /blog#contact');
                    }
                } else { //token dont match, throw a message
                    $this->setMessage('Erreur : Impossible d\'envoyer le message.', 'front-modal');
                    header('location: /blog#contact');
                }
            } else { //recaptcha return false
                $this->setMessage('Connexion impossible, merci de compléter tout les champs', 'auth');
                header('Location:'.$_SERVER['PHP_SELF']);
            }
        } else { //message not send, so display the form view
            echo $this->twig->render('contact_form.twig');
            $this->unsetMessage();
        }
    }
}
