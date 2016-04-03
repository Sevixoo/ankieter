<?php



namespace AppBundle\Controller\Basic;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends Controller
{

    private $em;

    public function __construct(){ }

    /**
     * Override method to call #containerInitialized method when container set.
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null){
        parent::setContainer($container);
        $this->containerInitialized();
    }

    public function getJSONResponse( $json , $resp_code = Response::HTTP_OK ){
        return new Response(json_encode($json), $resp_code ,
            array(
                'Content-Type' => 'application/json'
            ));
    }

    /**
     * Perform some operations after controller initialized and container set.
     */
    private function containerInitialized(){
        $this->em = $this->get('doctrine')->getManager();
        $this->onCreate();
    }

    public function onCreate(){
        /* */
    }

    public function getDoctrine(){
        return $this->em;
    }

    public function trace( $exp ){
        echo "<pre>";
        print_r($exp);
        echo "<pre/>";
    }

    public function getRequest(){
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    public function isAjax(){
        return $this->getRequest()->isXmlHttpRequest();
    }

    public function assertOnlyAjaxRequest(){
        if( ! $this->isAjax() )throw $this->createNotFoundException('Only ajax request!');
    }


}