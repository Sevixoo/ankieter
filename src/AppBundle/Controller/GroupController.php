<?php

namespace AppBundle\Controller;

use AppDataBundle\Entity\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Basic\BasicController;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GroupController extends BasicController
{
    /**
     * @Route("/group", name="group")
     */

    public function indexAction(Request $request)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        $groupFromForm = new Groups();

        $form = $this->createFormBuilder($groupFromForm)
            ->add('name', TextType::class, array('label' => 'Nazwa'))
            ->add('save', SubmitType::class, array('label' => 'ok', 'attr' => array('class' => 'modal-action modal-close waves-effect waves-green btn-flat')))
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            if (empty($groupFromForm->getName()))
            {
                $form->addError(new FormError('Nie podano nazwy'));

                return $this->render(':group:index.html.twig', array(
                    'groups' => $groups,
                    'form' => $form->createView(),
                    'isInvalid' => true,
                ));

            }
            if (!empty($this->getDoctrine()
                ->getRepository('AppDataBundle:Groups')->findOneBy(array('name' => $groupFromForm->getName()))))
            {
                $form->addError(new FormError('Grupa o podanej nazwie już isnieje'));

                return $this->render(':group:index.html.twig', array(
                    'groups' => $groups,
                    'form' => $form->createView(),
                    'isInvalid' => true,
                ));

            }

            else {

                $em = $this->getDoctrine();
                $em->persist($groupFromForm);
                $em->flush();

                return $this->redirectToRoute('group', array('isInvalid' => false));
            }



        }

        return $this->render(':group:index.html.twig', array(
            'groups' => $groups,
            'form' => $form->createView(),
            'isInvalid' => false,
        ));
    }

    /**
     * @Route("/group/{id}", name="groupIndex")
     */

    public function groupIndexAction(Request $request, $id)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        // replace this example code with whatever you need
        return $this->render(':group:index.html.twig', array(
            'groups' => $groups
        ));
    }

    /**
     * @Route("/group/{id}/pushCSV", name="groupPushCSV")
     */

    public function groupPushCSVAction(Request $request, $id)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        // replace this example code with whatever you need
        return $this->render(':group:index.html.twig', array(
            'groups' => $groups
        ));
    }

    /**
     * @Route("/group/{id}/pullCSV", name="groupPullCSV")
     */

    public function groupPullCSVAction(Request $request, $id)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        // replace this example code with whatever you need
        return $this->render(':group:index.html.twig', array(
            'groups' => $groups
        ));
    }

    public function groupAddAction(Request $request)
    {

        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll('SELECT * FROM Groups');

        // replace this example code with whatever you need
        return $this->render(':group:indexAdd.html.twig', array(
            'groups' => $groups
        ));
    }

    /**
     * @Route("/group/{id}/delete", name="groupDelete")
     */

    public function groupDeleteAction(Request $request, $id)
    {

        $conn = $this->get('database_connection');

        $conn->exec("DELETE FROM `Groups` WHERE `id` = $id");

        $groups = $conn->fetchAll('SELECT * FROM Groups');

        return $this->redirectToRoute('group');
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
