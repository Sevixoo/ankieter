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
            ->setFrom('ankieter@radasp34.ayz.pl')
            ->setTo('zychu312@gmail.com')
            ->setBody('<h1>Hello World</h1>')
            ->setContentType("text/html");
        $this->get('mailer')->send($message);


        return $this->render('default/index.html.twig');
        //return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/flush_mails", name="flush_mails")
     */

    public function flushMailsAction(){

        $mailer = $this->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->get('swiftmailer.transport.real');
        $transport->setPort(587);

        $spool->setMessageLimit(100);
        $spool->flushQueue($transport);

        return $this->redirectToRoute('homepage');
    }


}


