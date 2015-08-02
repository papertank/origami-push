<?php 

namespace Origami\Push;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Origami\Push\Device\DeviceInterface;
use Origami\Push\Exceptions\DeviceException;

class Push {

    protected $device;
    protected $user;
    protected $message;
    protected $badge;
    protected $sound = 'default';
    protected $action_key = 'Open';
    protected $params;

    protected function getData()
    {
        return array(
            'message' => $this->message,
            'badge' => $this->badge,
            'sound' => $this->sound,
            'action_key' => $this->action_key,
        );
    }

    public function push($token)
    {
        if ( ! $this->device ) {
            throw new DeviceException('No device set');
        }

        return $this->device->push($token, $this->getData(), $this->getParams());
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param mixed $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param Device $device
     */
    public function setDevice(DeviceInterface $device)
    {
        $this->device = $device;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}