<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller
{
    /**
     * @Route("/send_mail", name="send_mail")
     */

    public function sendMailAction(){

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo('zychu312@gmail.com')
            ->setBody('<h1>Hello World</h1>')
            ->setContentType("text/html");
        $this->get('mailer')->send($message);

        return $this->redirectToRoute('homepage');
    }

}
