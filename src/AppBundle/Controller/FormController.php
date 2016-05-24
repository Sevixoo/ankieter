<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class FormController extends Controller
{
    /**
     * @Route("/forms", name="forms")
     */

    /*
     * isActive:
     *  1 wystartowana / można zatrzymać
     */

    public function indexAction(){
        $conn = $this->get('database_connection');

        $forms = $conn->fetchAll("SELECT Forms.id AS formId, Forms.deadline, Forms.is_active, Forms.name AS formName, Forms.create_date, Forms.end_date,
                                  Templates.name AS templateName, Templates.id AS templateId
                                  FROM Forms
                                  LEFT JOIN Templates ON Forms.template_id = Templates.id
                                  ORDER BY Forms.is_active DESC, Forms.create_date
                                  ");
       // print_r($forms);


        return $this->render('forms/index.html.twig', array('forms' => $forms)
        );
    }

    /**
     * @Route("/forms/create", name="create_form")
     */
    public function createFormAction($errors = null){
        $conn = $this->get('database_connection');
        $doctrine = $this->getDoctrine();

        $templates = $conn->fetchAll("SELECT * FROM Templates ORDER BY Templates.create_date DESC");

        $groups = GroupController::getGroups($conn,$doctrine);

        return $this->render('forms/create_form.html.twig', array(
            'groups' => $groups,
            'templates' => $templates,
            'error' => $errors
        ) );
    }

    /**
     * @Route("/forms/new_form", name="new_form")
     */
    public function newFormAction(){
        $conn = $this->get('database_connection');
        $doctrine = $this->getDoctrine();
        $request = Request::createFromGlobals();

        $deadline = $request->request->get('end_date') + " 00:00:00";
        $start_date = "";//$request->request->get('start_date');
        $template_id = $request->request->get('template');
        $name = $request->request->get('form_name');
        $notify_type = $request->request->get('notify_type');


        $sql = "INSERT INTO `Forms`(`id`, `create_date`, `end_date`, `deadline`, `template_version`, `is_active`, `template_id`, `name`,`start_date` , `notify_type` )
            VALUES (null,NOW(),null,\"$deadline\",1,1,$template_id,\"$name\",\"$start_date\" , $notify_type )";

        $conn->exec($sql);

        return $this->indexAction();
    }

    /**
     * @Route("/forms/stop_form/{id}", name="stop_form")
     */
    public function stopFormAction($id){

        $conn = $this->get('database_connection');

        $sql = "UPDATE Forms
                SET is_active = '0'
                WHERE Forms.id = '$id'";

        $conn->exec($sql);

        return $this->indexAction();
    }

    /**
     * @Route("/forms/get_form_result/{id}", name="get_form_result")
     */
    public function getFormResultAction($id){

        $conn = $this->get('database_connection');
        $doctrine = $this->getDoctrine();
        $request = Request::createFromGlobals();

        $end_date = $request->request->get('end_date');
        $start_date = $request->request->get('start_date');
        $template_id = $request->request->get('template');
        $name = $request->request->get('form_name');

        $sql = "INSERT INTO `Forms`(`id`, `create_date`, `end_date`, `deadline`, `template_version`, `is_active`, `template_id`, `name`,`start_date`)
            VALUES (null,NOW(),\"$end_date\",\"\",1,0,$template_id,\"$name\",\"$start_date\" )";

        $conn->exec($sql);

        return $this->indexAction();
    }

}
