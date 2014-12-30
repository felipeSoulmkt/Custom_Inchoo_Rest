<?php
 
/**
 *
 * @author     Darko Goleš <darko.goles@inchoo.net>
 * @package    Inchoo
 * @subpackage RestConnect
 * 
 * Url of controller is: http://localhost/mudjeans/restconnect/test/[action] 
 */
class Inchoo_RestConnect_TestController extends Mage_Core_Controller_Front_Action {
 
    public function indexAction() {
 
        //Basic parameters that need to be provided for oAuth authentication
        //on Magento
        $params = array(
            'siteUrl' => 'http://localhost/mudjeans/oauth',
            'requestTokenUrl' => 'http://localhost/mudjeans/oauth/initiate',
            'accessTokenUrl' => 'http://localhost/mudjeans/oauth/token',
            'authorizeUrl' => 'http://localhost/mudjeans/admin/oAuth_authorize', //This URL is used only if we authenticate as Admin user type
            'consumerKey' => 'df0c57711f3ee354acb5c9193b5fa554', //Consumer key registered in server administration
            'consumerSecret' => 'bc72155935e9aebadc37426342bf14c8', //Consumer secret registered in server administration
            'callbackUrl' => 'http://localhost/mudjeans/restconnect/test/callback', //Url of callback action below
        );
 
 
        $oAuthClient = Mage::getModel('Inchoo_RestConnect/oauth_client');
        $oAuthClient->reset();
        $oAuthClient->init($params);
        $oAuthClient->authenticate();
 
        return;
    }
 
    public function callbackAction() {
 
        $oAuthClient = Mage::getModel('Inchoo_RestConnect/oauth_client');
        $params = $oAuthClient->getConfigFromSession();
        var_dump($params);
        if(!empty($params)){

            $oAuthClient->init($params);
            $state = $oAuthClient->authenticate();
     
            if ($state == Inchoo_RestConnect_Model_OAuth_Client::OAUTH_STATE_ACCESS_TOKEN) {
                
                $acessToken = $oAuthClient->getAuthorizedToken();

                if($acessToken){

                    $restClient = $acessToken->getHttpClient($params);
                // Set REST resource URL
                    $restClient->setUri('http://localhost/mudjeans/api/rest/products');
                    // In Magento it is neccesary to set json or xml headers in order to work
                    $restClient->setHeaders('Accept', 'application/json');
                    // Get method
                    $restClient->setMethod(Zend_Http_Client::GET);
                    //Make REST request
                    $response = $restClient->request();
                    // Here we can see that response body contains json list of products
                    echo "<pre>";
                    print_r($response); 
                    }
            
            }
     
           
        }
       
       // Zend_Debug::dump($response);
 
        return;
    }
 
}