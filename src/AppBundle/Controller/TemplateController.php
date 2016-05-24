<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TemplateController extends Controller
{


    /**
     * @Route("/templates", name="templates")
     */

    public function indexAction()
    {

        return $this->render('templates/index.html.twig', array( ) );
    }



}
