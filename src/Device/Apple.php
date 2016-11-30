<?php 

namespace Origami\Push\Device;

use Exception;
use Illuminate\Support\Facades\Log;
use Origami\Push\Exceptions\PushException;

class Apple implements DeviceInterface {

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
        $body = [];

        try {

            //CREATE THE PAYLOAD BODY Create the payload body
            $body['aps'] = array(
                'alert' => $data['message'],
                'sound' => $data['sound'],
                'extra'	=> $params,
            );

            //ENCODE PAYLOAD AS JSON
            $payload = json_encode($body);

            if ( strlen($payload) > 2048 ) {
                throw new PushException('Apple push payload cannot exceed 2048 bytes');
            }

            //CREATE THE CONNECTION
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->config['cert']);
            stream_context_set_option($ctx, 'ssl', 'cafile', $this->config['cafile']);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->config['pwd']);

            //OPEN CONNECTION TO THE APNS SERVER
            $fp = stream_socket_client('ssl://'.$this->config['host'].'', $err, $errstr, 30, STREAM_CLIENT_CONNECT, $ctx);

            if (!$fp) {
                unset($fp);
                Log::error('Failed to connect to APNS: $err $errstr');
                return false;
            }

            //BUILD THE BINARY NOTIFICATION
            $msg = chr(0).pack('n', 32).pack('H*', $token).pack('n', strlen($payload)).$payload;

            //SEND TO THE SERVER
            $result = fwrite($fp, $msg, strlen($msg));

            if ( ! $result ) {
                Log::error('iOS Message not delivered', $body);
            }

            //CLOSE SERVER CONNECTION
            fclose($fp);
            unset($fp);

            return $result;

        } catch ( Exception $e ) {
            Log::error($e, $body);
            return false;
        }
    }

}