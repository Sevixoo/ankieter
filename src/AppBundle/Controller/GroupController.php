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
        $users = $conn->fetchAll('SELECT * FROM Groups');

        print_r($users);
        // replace this example code with whatever you need
        return $this->render(':group:index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
