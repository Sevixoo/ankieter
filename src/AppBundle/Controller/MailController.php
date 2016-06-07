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

        // Reminder
        // -1 = no reminder
        // 1 codziennie
        // 2 co dwa dni
        // 3 co cztery dni
        // 4 tydzień
        // 5 miesiac

        $conn = $this->get('database_connection');
        $emailsToReminder = $conn->fetchAll("
        SELECT
        Subscribers.email as email,
        FormOutputs.last_mail_send_time as lastSend,
        Forms.notify_type as notify,
        FormOutputs.token as token,
        Forms.is_active as active,
        FormOutputs.output as output

        FROM FormOutputs

        LEFT JOIN Subscribers ON FormOutputs.subscriber_id = Subscribers.id
        LEFT JOIN Forms ON FormOutputs.form_id = Forms.id

        WHERE Forms.is_active = 1 AND FormOutputs.output IS NULL
            ");

        foreach($emailsToReminder as $item)
        {
            print_r($item['email']);

            $maxTime = 0;

            switch($item['notify'])
            {
                case 1: $maxTime = 1000 * 60 * 60 * 24; break;

                case 2: $maxTime = 1000 * 60 * 60 * 24 * 2; break;

                case 3: $maxTime = 1000 * 60 * 60 * 24 * 4; break;

                case 4: $maxTime = 1000 * 60 * 60 * 24 * 7; break;

                case 5: $maxTime = 1000 * 60 * 60 * 24 * 30; break;
                

            }



        }



        $mailer = $this->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->get('swiftmailer.transport.real');
        $transport->setPort(587);

        $spool->setMessageLimit(100);
        $spool->flushQueue($transport);

        //return $this->redirectToRoute('homepage');
        return $this->render('default/index.html.twig');

        // curl --request POST http://radasp34.ayz.pl/ankieta/web/flush_mails
    }


}


