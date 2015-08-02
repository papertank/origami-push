<?php 

namespace Origami\Push\Device;

use Exception;
use Illuminate\Support\Facades\Log;

class Android implements DeviceInterface {

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function push($token, $data, $params)
    {
        try {
            $url = 'https://'.$this->config['host'];

            $fields = array(
                'registration_ids' => array($token),
                'data' => array_merge($data,[
                    'data' => $params
                ]),
            );

            $headers = array(
                'Authorization: key='.$this->config['key'],
                'Content-Type: application/json'
            );
            // Open connection
            $ch = curl_init();

            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                Log::error('Curl failed: ' . curl_error($ch));
                return false;
            }

            // Close connection
            curl_close($ch);

            return true;
        } catch ( Exception $e ) {
            Log::error($e);
            return false;
        }
    }

}