<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Basic\BasicController;
use AppDataBundle\Entity\Groupssubscribers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\GroupController;

use AppDataBundle\Entity\Subscribers;

class CSVController extends Controller
{
    /**
     * @Route("/addCSV/{groupID}/{fileName}", name="addCSV")
     */
    public function addCSVAction(Request $request, $groupID, $fileName)
    {
        $warnings = array();
        $errors = array();
        $newEmails = array();

        try {
            $path = "bundles/tmp/csv/" . $fileName . ".csv";
            $file = fopen($path, "r");

            $tempElement = null;

            while (!feof($file)) {
                $tempElement = fgetcsv($file)[0];

                if (filter_var($tempElement, FILTER_VALIDATE_EMAIL)) {
                    array_push($warnings, "Blędny adres e-mail: " + $tempElement + "adres nie zostanie dodany do bazy");
                }
                elseif (!empty($tempElement)) array_push($newEmails, $tempElement);
            }
        } catch (\Exception $e) {
            array_push($errors, "Podano błędną ścieżkę do pliku" + $path);
            return CSVController::ErrorResponse($errors);
        } finally {
            print_r($newEmails);
            try {
                fclose($file);
            } catch (\Exception $e) {

            }
        }

        try {
            $conn = $this->get('database_connection');
            $actualEmailsInGroup = $conn->fetchAll("
            SELECT Subscribers.email
            FROM Subscribers
            INNER JOIN Groupssubscribers
            ON Subscribers.id=Groupssubscribers.idsubscriber
            WHERE Groupssubscribers.idgroup = '$groupID'
            ");

            $actualEmailsInGroupArray = array();

            $emailArray = array();

            foreach ($actualEmailsInGroup as $emailArray) {
                array_push($actualEmailsInGroupArray, $emailArray['email']);
            }

        } catch (\Exception $e) {
            array_push($errors, "Błąd połączenia z bazą danych");
            return CSVController::ErrorResponse($errors);
        }

        echo("<br>");
        print_r($actualEmailsInGroupArray);

        $commonPart = array_intersect($newEmails, $actualEmailsInGroupArray);
        echo("<br>");

        array_push($warnings,"Następujące adresy e-mail istnieją w tej grupie:");
        array_push($warnings, $commonPart);

        try {

            $allExisitngSubscribers = $this->getDoctrine()
                ->getRepository('AppDataBundle:Subscribers')
                ->findAll();

            $allExisitngEmails = array();

            foreach ($allExisitngSubscribers as $currentSubscriber) {
                array_push($allExisitngEmails, $currentSubscriber->getEmail());
            }

            echo("<br>");
            echo("Wszystkie adresy w bazie:");

            print_r($allExisitngEmails);
        } catch (\Exception $e) {
            array_push($errors, "Błąd połączenia z bazą danych");

            return CSVController::ErrorResponse($errors);
        }


        $emailsToAdd = array_diff($newEmails, array_intersect($newEmails, $allExisitngEmails));

        print_r("<br>");
        print_r("Adresy do dodania:");
        print_r($emailsToAdd);

        try {

            foreach ($emailsToAdd as $email) {
                $subscriber = new Subscribers();
                $subscriber->setEmail($email);
                $this->getDoctrine()->getEntityManager()->persist($subscriber);
            }

            $this->getDoctrine()->getEntityManager()->flush();

            $emailsToAddToGroup = array_intersect($newEmails, $commonPart);

            $group = $this->getDoctrine()->getRepository("AppDataBundle:Groups")->findOneByid($groupID);

            if(empty($group))
            {
                array_push($errors, "Podana grupa nie istnieje");
                CSVController::ErrorResponse($errors);
            }

            foreach ($emailsToAddToGroup as $email) {
                $groupsubscribers = new Groupssubscribers();
                $groupsubscribers->setIdgroup($group);

                $subscriber = $this->getDoctrine()->getRepository('AppDataBundle:Subscribers')->findOneByemail($email);
                $groupsubscribers->setIdsubscriber($subscriber);

                $this->getDoctrine()->getEntityManager()->persist($groupsubscribers);
            }

            $this->getDoctrine()->getEntityManager()->flush();

        } catch (\Exception $e) {
            array_push($errors, "Błąd połączenia z bazą danych");
            return CSVController::ErrorResponse($errors);
        }

        print_r("<br>");

        return CSVController::ErrorResponse($errors);
    }

    public function ErrorResponse($errors)
    {
        if (!empty($errors)) print_r($errors);
        return new Response("");
    }
}
