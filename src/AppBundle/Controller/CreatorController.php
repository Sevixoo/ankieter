<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\Basic\BasicController;

class CreatorController extends BasicController{

    /**
     * @Route("/templates/creator", name="tmpl_creator")
     */
    public function creatorAction(){


        return $this->render(':pages:creator.html.twig', array(

        ));
    }

}
