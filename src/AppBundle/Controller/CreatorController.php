<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\Basic\BasicController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppDataBundle\Entity\Groups;
use AppDataBundle\Entity\Groupssubscribers;
use Symfony\Component\Form\FormError;
use PDO;
use AppDataBundle\Entity\Subscribers;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Controller\CSVController;

class CreatorController extends BasicController{

    /**
     * @Route("/templates/creator", name="tmpl_creator")
     */
    public function creatorAction(){


        return $this->render(':pages:creator.html.twig', array(

        ));
    }

    /**
     * @Route("/api/creator/save", name="save_creator")
     * @Method({"POST"})
     */
    public function saveTemplateAction()
    {
        $request = Request::createFromGlobals();
        $template_id = $request->request->get('template_id');
        $template_html = $request->request->get('template_html');

        $conn = $this->get('database_connection');
        $creator_id = 1;
        $name = "";

        if() {
            $sql = "INSERT INTO `Templates`(`id`, `name`, `creator_id`, `fields_schema`, `output_schema`, `create_date`)
                VALUES (null,\"$name\",$creator_id,\"$template_html\",\"\",NOW())";

            if (empty($existingEmail)) {
                $conn->exec("INSERT INTO Subscribers (email) VALUE ('$subscriber_email')");
                $existingEmail = $conn->fetchAssoc("SELECT * FROM Subscribers WHERE email = '$subscriber_email'");
            }
        }

        $data = array(
            "success" => 1,
            "data" => array(
                "template_id" => 1
            )
        );


        return $this->getJSONResponse($data);
    }

}
