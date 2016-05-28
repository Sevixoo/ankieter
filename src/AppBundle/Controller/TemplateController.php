<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TemplateController extends Controller
{


    /**
     * @Route("/templates", name="templates")
     */

    public function indexAction(){
        $conn = $this->get('database_connection');
        $doctrine = $this->getDoctrine();

        $templates = $conn->fetchAll("SELECT * FROM Templates ORDER BY Templates.create_date DESC");


        return $this->render('templates/index.html.twig', array(
            'templates' => $templates
        ) );
    }

    /**
     *  @Route("/api/templates/view/{template_id}", name="view_templates")
     */
    public function viewTemplateAction($template_id){
        $conn = $this->get('database_connection');
        $template = $conn->fetchAssoc("SELECT * FROM `Templates` WHERE `id` = $template_id");

        return $this->render(':pages:form_view.html.twig', array(
            "token" => false,
            "title" => $template['name'],
            "template_id" => $template_id,
            "template_html" => $template['fields_schema']
        ));
    }


}
