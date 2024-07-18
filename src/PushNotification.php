<?php

namespace Origami\Push;

use Illuminate\Support\Arr;

class PushNotification
{
    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $subtitle;

    /**
     * @var string|null
     */
    protected $body;

    /**
     * @var int|null
     */
    protected $badge;

    /**
     * @var string|null
     */
    protected $sound;

    /**
     * @var string|null
     */
    protected $category;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $extra = [];

    public bool $isTest = false;

    public function __construct($body = '')
    {
        $this->body = $body;
    }

    public static function create()
    {
        return new self();
    }

    /**
     * @alias setBody
     *
     * @deprecated 4.0
     */
    public function message($body)
    {
        return $this->setBody($body);
    }

    /**
     * @alias setBadge
     *
     * @deprecated 4.0
     */
    public function badge($badge)
    {
        return $this->setBadge($badge);
    }

    /**
     * @alias setMeta
     *
     * @deprecated 4.0
     */
    public function meta($badge)
    {
        return $this->setMeta($badge);
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle = null)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    public function setBody(string $body = null)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBadge()
    {
        return $this->badge;
    }

    public function setBadge(int $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSound()
    {
        return $this->sound;
    }

    public function setSound(string $sound = null)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(string $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function setIsTest(bool $isTest): self
    {
        $this->isTest = $isTest;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    public function setExtra(array $extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @param  null  $fallback
     * @return mixed|null
     */
    public function getExtraValue($key, $fallback = null)
    {
        return Arr::get($this->extra, $key, $fallback);
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body,
            'badge' => $this->badge,
            'sound' => $this->sound,
            'category' => $this->category,
            'meta' => $this->meta,
            'extra' => $this->extra,
        ];
    }
}
