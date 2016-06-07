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

            $titles = $this->getTitles( $form['json_shema'] );
            $headers = $this->getHeaders( $form['json_shema'] );
            $names = $this->getIdsShema( $form['json_shema'] );

            // Add the header of the CSV file
            fputcsv($handle, $titles ,';');
            fputcsv($handle, $headers ,';');

            $results = $conn->fetchAll("SELECT * FROM `FormOutputs` WHERE `form_id` = $form_id AND `output` IS NOT NULL");


            // Add the data queried from database
            /*while($row = $results->fetch()) {
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

    private function getIdsShema( $schema ){
        $array = array();
        $array []= 0;
        $schema = json_decode( $schema , true );
        foreach( $schema as $k => $v ) {
            if (count($v['options']) > 0) {
                foreach ($v['options'] as $kk => $vv) {
                    $array[] = $v['name'];
                }
            } else {
                $array[] = $v['name'];
            }
        }
        return $array;
    }

    private function getTitles( $schema ){
        $array = array();
        $array []= "titles";
        $schema = json_decode( $schema , true );
        foreach( $schema as $k => $v ) {
            if (count($v['options']) > 0) {
                foreach ($v['options'] as $kk => $vv) {
                    $array[] = $v['title'];
                }
            } else {
                $array[] = $v['title'];
            }
        }
        return $array;
    }

    private function getHeaders( $schema ){
        $array = array();
        $array []= "options";
        $schema = json_decode( $schema , true );
        foreach( $schema as $k => $v ) {
            if (count($v['options']) > 0) {
                foreach ($v['options'] as $kk => $vv) {
                    $array[] = $vv["name"];
                }
            } else {
                $array[] = $v['title'];
            }
        }
        return $array;
    }

}
