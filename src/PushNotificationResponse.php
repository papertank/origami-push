<?php

namespace Origami\Push;

class PushNotificationResponse
{
    public $success;
    public $error;
    public $data;

    public function __construct(array $data = [], $success = true)
    {
        $this->data = $data;
        $this->success = (bool) $success;
    }

    public static function success(array $data = [])
    {
        return new static($data, true);
    }

    public static function error(array $data = [], $error = null)
    {
        return (new static($data, false))->setError($error);
    }

    public function isError()
    {
        return ! $this->isSuccessful();
    }

    public function isSuccessful()
    {
        return $this->success == true;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}
