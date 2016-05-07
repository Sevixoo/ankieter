<?php

namespace AppBundle\Controller;

use AppDataBundle\Entity\Groups;
use AppDataBundle\Entity\Groupssubscribers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Basic\BasicController;
use Symfony\Component\Form\FormError;
use PDO;
use AppDataBundle\Entity\Subscribers;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Controller\CSVController;

class GroupController extends BasicController
{
    /**
     * @Route("/group", name="group")
     */

    public function indexAction(Request $request)
    {
        $conn = $this->get('database_connection');

        $groups = array();

        $groups = $this->getDoctrine()->getRepository('AppDataBundle:Groups')->findAll();

        $allGroup = new Groups();
        $allGroup->setName("Wszyscy");
        $allGroup->setId(-1);

        array_unshift($groups, $allGroup);

        foreach($groups as $group)
        {
            if ($group->getId()==-1) {
                $value = $conn->fetchArray("SELECT COUNT(*) FROM Subscribers");
                $value = $value[0];
            }
            else {
                $id = $group->getId();
                $value = $conn->fetchArray("SELECT COUNT(*) FROM GroupsSubscribers WHERE GroupsSubscribers.idGroup = '$id'");
                $value = $value[0];
            }
            $group->setNumberOfSubscribers($value);
        }

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

    /*================================================================================================================*/
    /*  REST API                                                                                                      */
    /*================================================================================================================*/
    /**
     * @Route("/api/subscribers/{group_id}", name="get_subscribers")
     * @Method({"GET"})
     */
    public function getSubscribersAction($group_id){

        $list = array();
        $conn = $this->get('database_connection');
        if($group_id !=-1) {
            $list = $conn->fetchAll("
            SELECT Subscribers.email, Subscribers.id
            FROM Subscribers
            INNER JOIN GroupsSubscribers
            ON Subscribers.id=GroupsSubscribers.idsubscriber
            WHERE GroupsSubscribers.idgroup = '$group_id'");
        }
        else {
            $list = $conn->fetchAll("
            SELECT *
            FROM Subscribers");
        }

        $data = array(
            "success" => 1,
            "data" => array(
                "list" =>  $list,
                "badges" => $this->_getActualBadges(),
            )
        );

        return $this->getJSONResponse($data);
    }
    /**
     * @Route("/api/subscribers/delete", name="delete_subscribers")
     * @Method({"POST"})
     */
    public function deleteSubscribersAction(){
        $request = Request::createFromGlobals();
        $idsToDelete = json_decode( $request->request->get('ids') );
        return $this->_deleteSubscribers($idsToDelete);
    }
    /**
     * @Route("/api/subscribers/delete/{id}", name="delete_subscriber")
     * @Method({"POST"})
     */
    public function deleteSubscriberAction($id){
        $idsToDelete = array($id);
        return $this->_deleteSubscribers($idsToDelete);
    }

    private function _deleteSubscribers( $idsToDelete ){

        $conn = $this->get('database_connection');

        foreach($idsToDelete as $id) {
            $conn->exec("DELETE FROM Subscribers WHERE id = '$id' ");
            $conn->exec("DELETE FROM GroupsSubscribers WHERE idSubscriber = '$id' ");
        }

        $data = array(
            "success" => 1,
            "data" => array(
                "deleted_items" => $idsToDelete,
                "deleted_rows" => count($idsToDelete),
                "badges" => $this->_getActualBadges(),
            )
        );

        return $this->getJSONResponse($data);
    }

    /**
     * @Route("/api/subscribers/remove/{group_id}", name="remove_subscribers")
     * @Method({"POST"})
     */
    public function removeSubscribersFromGroupAction($group_id){
        $request = Request::createFromGlobals();
        $idsToDelete = json_decode( $request->request->get('ids') );
        $conn = $this->get('database_connection');

        foreach($idsToDelete as $id) {
            $conn->exec("DELETE FROM GroupsSubscribers WHERE idGroup = '$group_id' AND idSubscriber = '$id' ");
        }

        $data = array(
            "success" => 1,
            "data" => array(
                "removed_items" => $idsToDelete,
                "removed_rows" => count($idsToDelete),
                "badges" => $this->_getActualBadges(),
            )
        );

        return $this->getJSONResponse($data);
    }

    /**
     * @Route("/api/group/delete/{group_id}", name="delete_group")
     * @Method({"POST"})
     */
    public function deleteGroupAction($group_id){

        $conn = $this->get('database_connection');

        $deleted_rows =  $conn->exec("DELETE FROM `Groups` WHERE `id` = $group_id");

        $data = array(
            "success" => 1,
            "data" => array(
                "deleted_items" => array($group_id),
                "deleted_rows" => $deleted_rows,
                "badges" => $this->_getActualBadges(),
            ),
        );

        return $this->getJSONResponse($data);
    }

    /**
     * @Route("/api/group/del_sub/{group_id}", name="delete_group_and_subs")
     * @Method({"POST"})
     */
    public function deleteGroupAndSubsAction($group_id){

        $conn = $this->get('database_connection');

        if($group_id != -1)

        $subsToDelete = $conn->query("SELECT idSubscriber FROM GroupsSubscribers WHERE idGroup = $group_id")->fetchAll(PDO::FETCH_COLUMN);

        else

        $subsToDelete = $conn->query("SELECT id FROM Subscribers")->fetchAll(PDO::FETCH_COLUMN);

        $this->_deleteSubscribers($subsToDelete);

        if($group_id != -1)
        $deleted_rows =  $conn->exec("DELETE FROM `Groups` WHERE `id` = $group_id");

        else $deleted_rows = 0;

        $list = $conn->fetchAll("
        SELECT Subscribers.email, Subscribers.id
        FROM Subscribers
        INNER JOIN GroupsSubscribers
        ON Subscribers.id=GroupsSubscribers.idsubscriber
        WHERE GroupsSubscribers.idgroup = '$group_id'");


        $data = array(
            "success" => 1,
            "data" => array(
                "deleted_items" => array($group_id),
                "deleted_rows" => count(array($group_id)),
                "list" =>  $list,
                "badges" => $this->_getActualBadges(),
            )
        );

        if($group_id == -1) $data["data"]["deleted_items"] = [];

        return $this->getJSONResponse($data);
    }

    /**
     * @Route("/api/group/create", name="create_group")
     * @Method({"POST"})
     *
     * dodaje grupe jeśeli podamy file name to wczytuje z csv
     *
     */
    public function createGroupAction(){
        $request = Request::createFromGlobals();
        $group_name = $request->request->get('group_name');
        $file_name = $request->request->get('file_name');

        $conn = $this->get('database_connection');

        $existingGroup = $conn->fetchAssoc("SELECT * FROM `Groups` WHERE Groups.name = '$group_name'");

        if (!empty($existingGroup)) {
            $data = array(
                "success" => 0,
                "error" => "Grupa o podanej nazwie istnieje w bazie"
            );
            return $this->getJSONResponse($data);
        }

        $group = new Groups();
        $group->setName($group_name);

        $this->getDoctrine()->persist($group);
        $this->getDoctrine()->flush();

        $group_id = $group->getId();

        $csvOutput = array();

       if (!empty($file_name))
       {
           $csvOutput = $this->_addCSV($group_id, $file_name);
       }

        $group_size = $conn->fetchColumn("SELECT COUNT(*) FROM GroupsSubscribers WHERE idGroup = '$group_id'");

        if($group_id>0) {
            $data = array(
                "success" => 1,
                "data" => array(
                    "created" => array(
                        "id" => $group_id,
                        "name" => $group_name,
                        "size" => $group_size
                    ),
 //                   "import_report" => $this->_importMailsFromCSV($group_id, $file_name),
 //                   "badges" => $this->_getActualBadges(),
 //                   "csvOutput" => $csvOutput,
                )
            );
        }else{
            $data = array(
                "success" => 0,
                "error" => "COULD_NOT_ADD_GROUP"
            );
        }
        return $this->getJSONResponse($data);
    }

    /**
     * @Route("/api/group/csv/{group_id}", name="group_send_csv")
     * @Method({"POST"})
     */
    public function sendCSVGroupAction($group_id){
        $request = Request::createFromGlobals();
        $file_name = $request->request->get('file_name');
        $data = $this->_importMailsFromCSV( $group_id , $file_name );
        return $this->getJSONResponse($data);
    }

    private function _importMailsFromCSV( $_group_id , $_tmp_file_name ){
        //TODO... implement data model
        //wywołaj swojafunkcje do importu?
        $data = array(
            "success" => 1,
            "imported_count" => 1,
            "imported_errors" => 2,
            "imported_items" => array(
                "aaaa@bbb.cc" => "ALREADY_EXISTS",
                "aaasd@bbb.cc" => "OK",
                "aaasvvbbb.cc" => "PARSE_ERROR"
            )
        );

        return $data;
    }

    /**
     * @Route("/api/subscribers/create", name="create_subscribers")
     * @Method({"POST"})
     *
     *
     */
    public function createSubscribersAction(){
        $request = Request::createFromGlobals();

        $subscriber_email = $request->request->get('email');
        $group_id = $request->request->get('group_id');

        $conn = $this->get('database_connection');

        //Czy email istnieje w bazie

        $existingEmail = $conn->fetchAssoc("SELECT * FROM `Subscribers` WHERE email = '$subscriber_email'");

        if (empty($existingEmail)) {
            $conn->exec("INSERT INTO Subscribers (email) VALUE ('$subscriber_email')");
            $existingEmail = $conn->fetchAssoc("SELECT * FROM Subscribers WHERE email = '$subscriber_email'");
        }

        $existingEmailID = $existingEmail['id'];
        $existingEmail = $existingEmail['email'];


        $existingConnection = $conn->fetchAssoc("
        SELECT * FROM GroupsSubscribers WHERE idGroup = '$group_id' AND idSubscriber = '$existingEmailID'
        ");

        if (empty($existingConnection)) {
            if ($group_id !=-1)
            $conn->exec("INSERT INTO GroupsSubscribers(idGroup,idSubscriber) VALUE ('$group_id', '$existingEmailID')");
            $data = array(
                "success" => 1,
                "data" => array(
                    "created" => array(
                        "id" => $existingEmailID,
                        "email" => $existingEmail,
                        "badges" => $this->_getActualBadges(),
                    ),

                )
            );
        }

        else
        {
            $data = array(
                "success" => 0,
                "error" => "Podany adres istnieje w tej grupie",
            );

        }

        return $this->getJSONResponse($data);
    }

    private function _getActualBadges()
    {
        $conn = $this->get('database_connection');
        $groups = $conn->fetchAll("SELECT id FROM Groups");
        $badges = array();

        //Grupa Wszyscy
        $badges[] = array('id'=>-1, 'size'=>($conn->fetchColumn("SELECT COUNT(*) FROM Subscribers")));

        $conn->close();

        //Reszta Grup
        foreach ($groups as $group)
        {
            $group = $group['id'];
            $size = $conn->fetchColumn("SELECT COUNT(*) FROM GroupsSubscribers WHERE idGroup = '$group'");
            $conn->close();
            $badges[] = array('id'=>$group, 'size'=>$size);
        }
        return $badges;
    }

    private function  _addCSV($groupID, $fileName)
    {
        $path = ("bundles/tmp/images/" . $fileName);
        $file = fopen($path, "r");

        $tempElement = null;
        $newEmails = array();

        while (!feof($file)) {
            $tempElement = fgetcsv($file)[0];
//                Sprawdzenie poprawności emaili
//                if (filter_var($tempElement, FILTER_VALIDATE_EMAIL)) {
//                    array_push($warnings, "Blędny adres e-mail: " + $tempElement + "adres nie zostanie dodany do bazy");
//                }
//                elseif (!empty($tempElement))
            array_push($newEmails, $tempElement);
        }

        fclose($file);

        $conn = $this->get('database_connection');

        $sql = "SELECT email FROM Subscribers";
        $existingEmails = $conn->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        $emailsToAdd = array();


        foreach ($newEmails as $newEmail) {
            if (!in_array($newEmail, $existingEmails)) {
                array_push($emailsToAdd, $newEmail);
            }
        }

        foreach ($emailsToAdd as $emailToAdd) {
            $conn->exec("
            INSERT INTO Subscribers (email) VALUE ('$emailToAdd')
            "
            );

        }

        $subscribersId = array();

        foreach ($newEmails as $newEmail)
        {
            array_push($subscribersId, $conn->fetchColumn("SELECT id FROM Subscribers WHERE email = '$newEmail'"));
        }


        foreach($subscribersId as $subscriberId)
        {
            $conn->exec("INSERT INTO GroupsSubscribers (idGroup,idSubscriber) VALUE ('$groupID','$subscriberId')");
        }
    }
}
