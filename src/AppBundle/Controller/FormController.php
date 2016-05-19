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

    public function indexAction(Request $request)
    {
        $conn = $this->get('database_connection');

        $forms = $conn->fetchAll("SELECT Forms.id, Forms.deadline, Forms.is_active, Forms.name AS formName, Forms.create_date, Forms.end_date,
                                  Templates.name AS templateName
                                  FROM Forms
                                  LEFT JOIN Templates ON Forms.template_id = Templates.id
                                  ORDER BY Forms.is_active DESC, Forms.create_date
                                  ");
       // print_r($forms);


        return $this->render('forms/index.html.twig', array('forms' => $forms)
        );
    }


}
