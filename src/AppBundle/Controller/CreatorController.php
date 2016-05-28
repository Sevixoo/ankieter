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
        $template_html = trim($request->request->get('template_html'));
        $template_json = trim($request->request->get('template_json'));
        $form_desc =  $request->request->get('form_desc');
        $name = $request->request->get('name');

        $conn = $this->get('database_connection');
        $creator_id = 1;

        if($template_id<=0) {
            $sql = "INSERT INTO `Templates`(`id`, `name`, `creator_id`, `fields_schema`, `output_schema`, `create_date` , `json_shema` , `form_desc` )
                VALUES (null,\"$name\",$creator_id, ".$conn->quote($template_html)." ,\"\",NOW() , ".$conn->quote($template_json)." ,\"$form_desc\" )";
            $conn->exec($sql);
            $template_id = $conn->lastInsertId();
        }else{
            $sql = "UPDATE `Templates` SET
                `name`=\"$name\",
                `creator_id`=$creator_id,`fields_schema`=".$conn->quote($template_html).",
                `output_schema`=\"\",
                `json_shema`=".$conn->quote($template_json).",
                `create_date`=NOW(),
                `form_desc`= \"$form_desc\"
                WHERE `id` = " .$template_id ;

            $ret = $conn->exec($sql);
            if($ret==0){
                $data = array(
                    "success" => 0,
                    "error" => "UPDATE ERROR",
                    "debug" => array(
                        "sql" => $sql
                    )
                );
                return $this->getJSONResponse($data);
            }
        }

        $data = array(
            "success" => 1,
            "data" => array(
                "template_id" => $template_id
            ),
            "debug" => array(
                "sql" => $sql
            )
        );

        return $this->getJSONResponse($data);
    }

}
