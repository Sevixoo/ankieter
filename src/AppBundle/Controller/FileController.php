<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\Basic\BasicController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\Basic\UploadHandler;

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
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i'
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

}
