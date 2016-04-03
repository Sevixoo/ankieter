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

    /*
     * Przykładowo:
     *
     * public function updateApplyStateAction(){
        $p = new PlayersModel( $this->getDoctrine() );

        $data= array(
            "data" => $p->updateApplyState( $_POST["id_player_tour"] , $_POST["accepted"] , $_POST["active"] )
        );
        return $this->getJSONResponse($data);
        LUB return $this->getJSONResponse($data,Response::HTTP_BAD_REQUEST);// inny kod odp dla błędu
    }
     *
     * wykorzystanie kodów Http
     *
     * np: Response::HTTP_OK
     *
     *
     * odp z API zawsze:
     * code400=>
     * {
     *  "success" = 1,
     *  "data" = $twoje_dane np id dodanego rekordu lub czy został usunięty
     * }
     * code!=400
     * {
     *  "success" = 0,
     *  "error" = string
     *   error_code = int
     * }
     * */
}
