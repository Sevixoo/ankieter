<?php

namespace AppBundle\Controller;
use AppBundle\Controller\Basic\BasicController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CSVController extends Controller
{


    /**
     * @Route("/addCSV/{groupID}/{fileName}", name="addCSV")
     */
    public function addCSVAction(Request $request, $groupID, $fileName)
    {
        $list = [];
        $path = "bundles/tmp/csv/" . $fileName . ".csv";

        $file = fopen($path,"r");
        $tempElement = null;

        while(! feof($file))
        {
            $tempElement = fgetcsv($file)[0];
            if (!empty($tempElement)) array_push($list,$tempElement);
        }

        print_r($list);
        fclose($file);

//        $repository = $this->getDoctrine()
//            ->getRepository('AppDataBundle:Subscribers');
//
//        $query = $repository->createQueryBuilder('p')
//            ->getQuery();
//
//        $products = $query->getResult();
//
//        print_r($products);


        $conn = $this->get('database_connection');
        $actualEmailsinGroup = $conn->fetchAll("
        SELECT Subscribers.email
        FROM Subscribers
        INNER JOIN Groupssubscribers
        ON Subscribers.id=Groupssubscribers.idsubscriber
        WHERE Groupssubscribers.idgroup = '$groupID'
        ");

        print_r($actualEmailsinGroup);



        return new Response("");
    }
}
