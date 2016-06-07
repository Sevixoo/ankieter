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
        FormOutputs.output as output,
        DATEDIFF(CURDATE(),FormOutputs.last_mail_send_time) AS diffDate

        FROM FormOutputs

        LEFT JOIN Subscribers ON FormOutputs.subscriber_id = Subscribers.id
        LEFT JOIN Forms ON FormOutputs.form_id = Forms.id

        WHERE Forms.is_active = 1 AND FormOutputs.output IS NULL
            ");


        $emailsToSendReminder = [];

        foreach($emailsToReminder as $item)
        {
            $maxTime = 0;

            switch($item['notify'])
            {
                case 1: $maxTime = 1; break;

                case 2: $maxTime = 2; break;

                case 3: $maxTime = 4; break;

                case 4: $maxTime = 7; break;

                case 5: $maxTime = 30; break;

            }


            if ($item['diffDate'] >= $maxTime)
                array_push($emailsToSendReminder,$item);

        }

        $conn = $this->get('database_connection');
        $mailer = $this->get('mailer');

        foreach($emailsToSendReminder as $item)
        {
            $link = "http://ankieta.radasp34.ayz.pl/web/forms/output/" . $item['token'];

            $message = \Swift_Message::newInstance()
                ->setSubject('Ankieta')
                ->setFrom('ankieter@radasp34.ayz.pl')
                //->setTo('zychu312@gmail.com')
                 ->setTo($item['email'])
                ->setBody(

                    $this->renderView(
                        'mails/reminderForm.html.twig',
                        array('link' => $link)
                    ),
                    'text/html'
                )
                ->setContentType("text/html");
            $mailer->send($message);

            $token = $item['token'];

            $sql = "UPDATE FormOutputs SET last_mail_send_time = CURDATE() WHERE token = '$token'";

            $conn->exec($sql);

        }


        $mailer = $this->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->get('swiftmailer.transport.real');
        $transport->setPort(587);

        $spool->setMessageLimit(300);
        $spool->flushQueue($transport);

        return $this->render('default/index.html.twig');

    }


}


