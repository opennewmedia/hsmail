<?php
namespace ONM\Hsmail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class HotelsuiteApi {

    private $accessToken;
    private $extensionConfiguration = [];
    
    protected  $endpoint;
    protected  $data;
    protected  $attemp = 0;
    protected $logger;

    public function __construct() 
    {
        // Load Extension Configuration
        $this->extensionConfiguration = $this->getExtensionConfiguration();
        $this->extensionConfiguration['apiUrl'] = 'https://tools.hotelsuite.de/'.$this->extensionConfiguration['instance'].'/api';
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\Extbase\\Object\\ObjectManager');
        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        
    }

    /**
     * fetchData
     *
     * @return array $data
     */
    public function fetch($endpoint)
    {
        // endpoint url
        $url = $this->extensionConfiguration['apiUrl'] . $endpoint;
        // Get Access Token
        $this->accessToken = $this->getCurrentAccessToken();
        // have to fetch data from API with accessToken and endpoitn
        $this->data = $this->fetchData($url, $this->accessToken);

        return $this->data;
    }

    public function fetchData($endpoint = FALSE, $accessToken = FALSE)
    {
        
        if (!$endpoint || !$accessToken) {
            return;
        }
               
        // Prepare Headers for Request
        $headers= [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/vnd.hotel-suite.v1+json'
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $data = curl_exec($ch);      
        
        // If a 403 status (error) is returned, try to get a new token (3-attemps)
        $statusCode = curl_getinfo($ch)['http_code'];

        // close curl connection
        curl_close($ch);

        if (($statusCode == '403' || $statusCode == '401') && $this->attemp < 3) {

            $this->attemp++;

            // Update Access token, and save new token in db
            $this->updateAccessToken();

            // Fetch again, using the new (updated) token from db
            $data = $this->fetchData($endpoint, $this->getCurrentAccessToken());

        } else if ($this->attemp >= 3) {
            $msg = 'API AUTHENTICATION ERROR: Invalid client_id or client_secret. Check extension configuration!';
            DebugUtility::debug($msg);
        }

        return $data;

    }


    /**
     * getCurrentAccessToken
     *
     * @return $string accessToken
     */
    public function getCurrentAccessToken() 
    {

        // Try to get existing token from DB
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_hsmail_config')
                ->select(
                    ['config', 'value'],                // fields to select
                    'tx_hsmail_config',               // from
                    [ 'config' => 'access_token' ]      // where
                )
                ->fetch();

        if (isset($row['value']) && !empty($row['value'])) {
            return $row['value'];                       // Return access token if found
        } else {
            $this->updateAccessToken();                 // Store new access token if not found
        }

    }

    /**
     * updateAccessToken
     *
     * @return void
     */
    public function updateAccessToken() 
    {

        $this->accessToken = $this->getNewAccessToken();

        // Save the new Token to DB
        if ($this->accessToken) {
            
            GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_hsmail_config')
            ->delete(
                'tx_hsmail_config',          // from
                [ 'config' => 'access_token' ] // where
            );

            GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_hsmail_config')
                ->insert(
                    'tx_hsmail_config',
                    [
                        'config' => 'access_token',
                        'value'  => $this->accessToken,
                    ]
                );
        }

    }

    /**
     * getNewAccessToken
     *
     * @return void
     */
    public function getNewAccessToken() 
    {

        $accessToken = false;

        $requestData = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->extensionConfiguration['clientId'],
            'client_secret' => $this->extensionConfiguration['clientSecret']
        ];
        
        $url = $this->extensionConfiguration['apiUrl'] . '/auth';
        
        $headers= [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $data = curl_exec($ch);
        
        if ((curl_getinfo($ch)['http_code'] != '200')) {
            $msg = 'API AUTHENTICATION ERROR: ' . 'Title: ' . json_decode($data, TRUE)['title'] . '. Status: ' . json_decode($data, TRUE)['status'] . '. Details: ' . json_decode($data, TRUE)['detail'];
            DebugUtility::debug($msg);
        } else {
            $accessToken = json_decode($data, TRUE)['data']['token'];
        }
        curl_close($ch);

        return $accessToken;
    }



    protected function getExtensionConfiguration()
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('hsmail');

        if($extensionConfiguration['enableAPI']) {
            if (!$extensionConfiguration['clientId'] || !$extensionConfiguration['clientSecret'] || !$extensionConfiguration['apiUrl']) {
                $extensionConfiguration['clientId'] = '';
                $extensionConfiguration['clientSecret'] = '';
                DebugUtility::debug('Either clientId or/and clientSecret or/and apiURL is missing.');
            }
        }

        return $extensionConfiguration;
    }

}