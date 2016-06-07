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
    public function generateCsvAction($form_id){
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

            $shema = json_decode( $form['json_shema'] , true );


            $results = array();
            $results_tmp = $conn->fetchAll("SELECT `output` FROM `FormOutputs` WHERE `form_id` = $form_id AND `output` IS NOT NULL");
            foreach($results_tmp as $r ){
                $results[]=  json_decode( $r['output'] , true );
            }

            $titles = array();
            $headers = array();
            $names = array();
            $sums = array();
            $sumsSorted = array();

                foreach ($shema as $k => $v) {
                    $options = $v['options'];
                    $type = $v['type'];
                    $name = $v['name'];
                    $title = $v['title'];
                    if (count($options) > 0) {
                        foreach ($options as $opt) {
                            $opt_name = $opt["name"];
                            $opt_value = $opt["value"];
                            $out_name = $name . $opt_name;
                            $titles[] = $title;
                            $headers[] = $opt_name;
                            $names[] = $out_name;
                            foreach ($results as $res) {
                                foreach ($res as $r_key => $r_val) {
                                    if (array_key_exists($name , $r_val )) {
                                        $res_val = $r_val[$name];
                                        if ($type == "RadioGroup") {
                                            if ($res_val == $opt_value) {
                                                if (array_key_exists( $out_name, $sums)) {
                                                    $sums[$out_name]++;
                                                } else {
                                                    $sums[$out_name] = 1;
                                                }
                                            }
                                        } else if ($type == "ListView") {
                                            foreach ($res_val as $res_val_i) {
                                                if ($res_val_i == $opt_value) {
                                                    if (array_key_exists( $out_name, $sums)) {
                                                        $sums[$out_name]++;
                                                    } else {
                                                        $sums[$out_name] = 1;
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $titles[] = $title;
                        $headers[] = $title;
                        $names[] = $name;
                        $sums[$name] = 0;
                    }
                }


                foreach ($names as $n) {
                    if( array_key_exists( $n, $sums) ){
                        $sumsSorted[] = $sums[$n];
                    }else {
                        $sumsSorted[] = "";
                    }
                }



            fputcsv($handle, $titles ,';');
            fputcsv($handle, $headers ,';');
            fputcsv($handle, $sumsSorted ,';');


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
