<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends Controller
{
    /**
     * @Route("/group", name="group")
     */

    public function indexAction(Request $request)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        // replace this example code with whatever you need
        return $this->render(':group:index.html.twig', array(
            'groups' => $groups
        ));
    }
}
