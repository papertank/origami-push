<?php

namespace Origami\Push;

class PushNotification
{

    /**
     *
     * @var string|null
     */
    public $message;

    /**
     *
     * @var string|null
     */
    public $badge;

    /**
     *
     * @var string|null
     */
    public $sound;

    /**
     *
     * @var string|null
     */
    public $action;

    /**
     *
     * @var array
     */
    public $meta = [];

    public function __construct($message = '')
    {
        $this->message = '';
    }

    /**
     *
     * @param  string  $message
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     *
     * @param  string  $badge
     * @return $this
     */
    public function badge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     *
     * @param  string  $sound
     * @return $this
     */
    public function sound($sound)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     *
     * @param  string  $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     *
     * @param  array  $meta
     * @return $this
     */
    public function meta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function toArray()
    {
        return [
            'message' => $this->message,
            'badge' => $this->badge,
            'sound' => $this->sound,
            'action' => $this->action,
            'meta' => $this->meta,
        ];
    }
}
