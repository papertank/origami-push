<?php

namespace Origami\Push\Drivers;

use Exception;
use Origami\Push\Driver;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Illuminate\Support\Facades\Log;

class Apns extends Driver {

    /**
     * @var array
     */
    private $config;

    /**
     * @var int
     */
    private $environment;

    const SANDBOX = 0;
    const PRODUCTION = 1;

    public function __construct(array $config = [], $environment = null)
    {
        $this->config = $config;
        $this->environment = $environment;

        if ( is_null($this->environment) ) {
            $this->environment = app()->environment('production') ? self::PRODUCTION : self::SANDBOX;
        }
    }

    public function send(Device $device, PushNotification $notification)
    {
        $body = [];

        $fp = null;

        try {

            //CREATE THE PAYLOAD BODY Create the payload body
            $body['aps'] = array(
                'alert' => data_get($notification, 'message'),
                'sound' => data_get($notification, 'sound', 'default'),
                'extra'	=> data_get($notification, 'meta', []),
            );

            //ENCODE PAYLOAD AS JSON
            $payload = json_encode($body);

            if ( strlen($payload) > 2048 ) {
                throw new Exception('Apple push payload cannot exceed 2048 bytes');
            }

            $certificate = array_get($this->config, 'certificate');
            $passphrase = array_get($this->config, 'passphrase');
            $cafile = array_get($this->config, 'cafile');

            if ( empty($certificate) ) {
                throw new Exception('The certificate and/or passphrase for APNS is not set in the config');
            }

            if ( ! file_exists($certificate) ) {
                throw new Exception('The certificate path does not exist');
            }

            //CREATE THE CONNECTION
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate);
            if ( $passphrase ) {
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
            }
            if ( $cafile ) {
                stream_context_set_option($ctx, 'ssl', 'cafile', $cafile);
            }

            //OPEN CONNECTION TO THE APNS SERVER
            $fp = stream_socket_client('ssl://'.$this->getEnvironmentHost(), $err, $errstr, 30, STREAM_CLIENT_CONNECT, $ctx);

            if (!$fp) {
                unset($fp);
                throw new Exception('Failed to connect to APNS: ' . $err . ', ' . $errstr);
            }

            //BUILD THE BINARY NOTIFICATION
            $msg = implode('', [
                chr(0),
                pack('n', 32),
                pack('H*', $device->getPushToken()),
                pack('n', strlen($payload)),
                $payload
            ]);

            //SEND TO THE SERVER
            $result = fwrite($fp, $msg, strlen($msg));

            if ( ! $result ) {
                Log::debug('APSN Message not delivered', $body);
            }

            //CLOSE SERVER CONNECTION
            fclose($fp);
            unset($fp);

            return $result;

        } catch ( Exception $e ) {
            if ( $fp ) {
                fclose($fp);
                unset($fp);
            }

            throw $e;
        }
    }

    private function getEnvironmentHost()
    {
        switch ( $this->environment ) {
            case self::SANDBOX:
                return 'gateway.sandbox.push.apple.com:2195';
                break;
            case self::PRODUCTION:
                return 'gateway.push.apple.com:2195';
                break;
            default:
                throw new Exception('Invalid APNS environment: '.$this->environment);
        }
    }

}
