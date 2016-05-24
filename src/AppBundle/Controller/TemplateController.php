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



}
