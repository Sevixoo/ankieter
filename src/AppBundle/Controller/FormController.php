<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Basic\BasicController;

class FormController extends BasicController
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


        return $this->render('forms/index.html.twig', array('forms' => $forms));
    }


    /**
     * @Route("/forms/outputs/{form_id}/{page}", name="outputs")
     */
    public function outputsAction( $form_id , $page = 0){
        $conn = $this->get('database_connection');

        $sql = "SELECT * , FormOutputs.id as output_id , Templates.name as templateName FROM `FormOutputs`
                JOIN Forms ON Forms.id = form_id
                JOIN Templates ON Forms.template_id = Templates.id
                JOIN Subscribers ON FormOutputs.subscriber_id = Subscribers.id
                WHERE Forms.id = $form_id
                LIMIT ".(50*$page).",50";

        $outputs = $conn->fetchAll($sql);

        return $this->render('forms/outputs.html.twig', array('outputs' => $outputs));
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

        $form_id = $conn->lastInsertId();

        $this->trace($_POST);
        $groups = implode( "," , $_POST['groups'] );

        $in_array = false;
        foreach($_POST['groups'] as $item)
            if($item == -1 || $item == "-1")$in_array = true;


        if( !$in_array ) {
            $sql = "SELECT DISTINCT Subscribers.id , Subscribers.email FROM `GroupsSubscribers`
                JOIN Subscribers ON Subscribers.id = idSubscriber
                WHERE `idGroup` IN ( $groups )";
        }else{
            $sql = "SELECT Subscribers.id , Subscribers.email FROM `Subscribers`";
        }

        $users= $conn->fetchAll($sql);

        foreach($users as $u){
            $UUID = $this->gen_uuid();
            $id = $u['id'];
            $sql = "INSERT INTO `FormOutputs`(`id`, `subscriber_id`, `form_id`, `token`, `output`, `pre_output` , `last_mail_send_time` )
                    VALUES (null,$id,$form_id,\"$UUID\",null,null,null)";

            ///$UUID
            $conn->exec($sql);

            $link = "http://ankieta.radasp34.ayz.pl/web/forms/output/" . $UUID;

            $message = \Swift_Message::newInstance()
                ->setSubject('Ankieta')
                ->setFrom('ankieter@radasp34.ayz.pl')
                ->setTo($u['email'])
                ->setBody('<a href="' . $link . '"LINK</a>')
                ->setContentType("text/html");
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Ankieta')
                ->setFrom('ankieter@radasp34.ayz.pl')
                ->setTo('zychu312@gmail.com')
                ->setBody(

                    $this->renderView(
                        'mails/newForm.html.twig',
                        array('link' => $link)
                    ),
                    'text/html'
                )
                ->setContentType("text/html");


            $this->get('mailer')->send($message);

            $token = $u['token'];

            $sql = "UPDATE FormOutputs SET last_mail_send_time = CURDATE() WHERE token = '$token'";

            $conn->exec($sql);


        }

        return $this->redirectToRoute('forms');
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

        return $this->redirectToRoute('forms');
    }



    /**
     * @Route("/forms/send/error/{message}", name="form_error")
     */
    public function formErrorAction($message){
        return $this->render(':forms:form_view_error.html.twig', array(
            "message" => $message
        ));
    }

    /**
     * @Route("/forms/send/success/{message}", name="form_success")
     */
    public function formSuccessAction($message){
        return $this->render(':forms:form_view_success.html.twig', array(
            "message" => $message
        ));
    }

    /**
     * @Route("/forms/edit/{token}", name="form_edit")
     */
    public function formEditAction($token){
        $conn = $this->get('database_connection');
        $userOutput = $this->_getUserByToken($conn,$token);
        if( $userOutput == null ){
            return $this->redirectToRoute( "form_error" , array('message'=>"Nie znaleziono użytkownika") );
        }
        $template = $this->_getTemplateById($conn,$userOutput['template_id']);
        if( $template == null ){
            return $this->redirectToRoute( "form_error" , array('message'=>"Nie znaleziono szablonu") );
        }

        $template_id = $userOutput['template_id'];

        $pageData = array(
            "isEdit" => true,
            "data" => $userOutput['output'],
            "token" => $token,
            "title" => $template['name'],
            "template_id" => $template_id,
            "template_html" => $template['fields_schema']
        );

        return $this->render(':pages:form_view.html.twig', $pageData );
    }

    /**
     * @Route("/forms/send/output/{token}", name="form_output")
     */
    public function formOutputAction($token){
        $conn = $this->get('database_connection');

        $userOutput = $this->_getUserByToken($conn,$token);
        $isEdit = isset($_POST['isEdit']);

        if( $userOutput == null ){
            return $this->redirectToRoute( "form_error" , array('message'=>"Nie znaleziono użytkownika") );
        }
        if( $userOutput['output'] != null && !$isEdit ){
            return $this->redirectToRoute( "form_error" , array('message'=>"Formularz został już wypełniony") );
        }
        $template = $this->_getTemplateById($conn,$userOutput['template_id']);

        if( $template == null ){
            return $this->redirectToRoute( "form_error" , array('message'=>"Nie znaleziono szablonu") );
        }

        $template_id = $userOutput['template_id'];

        $pageData = array(
            "isEdit" => false,
            "token" => $token,
            "title" => $template['name'],
            "template_id" => $template_id,
            "template_html" => $template['fields_schema']
        );

        if( isset( $_POST['submit'] ) ){

            $output = $this->_validateForm( json_decode( $template['json_shema'] , true));

            if(count($output['errors'])==0){
                $ret = $this->_insertFormOutput( $conn , $userOutput , json_encode( $output['data'] ) );
                if($ret){
                    if(!$isEdit) {
                        return $this->redirectToRoute("form_success", array('message' => "Formularz został wysłany"));
                    }
                }else{
                    return $this->redirectToRoute( "form_error" , array('message'=>"Błąd aplikacji") );
                }
            }

            $pageData['errors']= json_encode($output['errors']);
            $pageData['data']= json_encode($output['data']);
        }

        return $this->render(':pages:form_view.html.twig', $pageData );
    }

    private function _validateForm( $schema ){
        $data = array();
        $errors = array();

        foreach( $schema as $k => $v ){
            $name = $v['name'];
            if( isset($_POST[$name]) && !empty($_POST[$name]) ){
                $data[]= array(
                    $name => $_POST[$name]
                );
            }else if($v['required']=="required"){
                $errors[]= array(
                    $name => "Pole wymagane"
                );
            }else{
                $data[]= array(
                    $name => ""
                );
            }
        }

        $errors = array(
            'errors' => $errors,
            'data' => $data
        );

        return $errors;
    }

    private function _insertFormOutput( $conn , $userOutput , $output ){
        try {
            $sql = "UPDATE `FormOutputs` SET `output`=".$conn->quote($output)." WHERE `id`=".$userOutput['output_id'];
            return $conn->exec($sql);
        }catch (\Exception $ex){
            return false;
        }
    }

    private function _getUserByToken( $conn, $token ){
        $sql = "SELECT * , FormOutputs.id as output_id FROM `FormOutputs`
                JOIN Forms ON Forms.id = form_id
                WHERE `token` =  \"$token\"";
        $user = $conn->fetchAssoc($sql);
        return $user;
    }

    private function _getTemplateById( $conn, $template_id ){
        $template = $conn->fetchAssoc("SELECT * FROM `Templates` WHERE `id` = $template_id");
        return $template;
    }

    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }


}
