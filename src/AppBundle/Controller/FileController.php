<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\Basic\BasicController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\Basic\UploadHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends BasicController
{

    /**
     * @Route("/api/upload_tmp_file", name="upload_tmp_file")
     */
    public function uploadTmpFileAction(){

        $upload_handler = new UploadHandler( array(
            'upload_dir' => './bundles/tmp/images/',
            'upload_url' => './bundles/tmp/images/',
            'max_file_size' => 2500000,
            'accept_file_types' => '/\.(csv)$/i'
        ) );

        $response = $upload_handler->get_response();
        $response['file'] = $response['files'][0];
        unset($response['files']);

        if( ! @$response['file']->error ){
            $response['name'] = $response['file']->name;
            $response['success'] = 1;
            return $this->getJSONResponse( $response );
        }else{
            $response['success'] = 0;
            return $this->getJSONResponse( $response );
        }
    }

    public $form_id = -1;
    /**
     * @Route("/file/get_results/{form_id}", name="get_results")
     */
    public function generateCsvAction($form_id)
    {
        $this->form_id = $form_id;

        $response = new StreamedResponse();
        $response->setCallback(function() {
            $handle = fopen('php://output', 'w+');
            $form_id = $this->form_id;

            $conn = $this->get('database_connection');

            $sql = "SELECT * , Forms.id as form_id , Templates.id as tmpl_id
                FROM `Forms`
                JOIN Templates ON Templates.id = Forms.template_id
                WHERE Forms.id = ".$form_id;
            $form = $conn->fetchAssoc($sql);

            $headers = $this->getHeaders( $form['json_shema'] );

            // Add the header of the CSV file
            fputcsv($handle, $headers ,';');

            /*$results = $this->connection->query("Replace this with your query");
            // Add the data queried from database
            while($row = $results->fetch()) {
                fputcsv(
                    $handle, // The file pointer
                    array($row['name'], $row['surname'], $row['age'], $row['sex']), // The fields
                    ';' // The delimiter
                );
            }*/

            fclose($handle);
        });


        $conn = $this->get('database_connection');
        $sql = "SELECT Templates.name as name  FROM `Forms`
                JOIN Templates ON Templates.id = Forms.template_id
                WHERE Forms.id = ".$form_id;
        $res = $conn->fetchAssoc($sql);

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$res['name'].".csv".'" ');

        return $response;
    }

    private function getHeaders( $schema ){
        $array = array();
        $schema = json_decode( $schema , true );
        foreach( $schema as $k => $v ){
            $array[]=  $v['title'];
        }
        return $array;
    }

}
