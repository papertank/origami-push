<?php

namespace Origami\Push\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function projectIdNotSpecified()
    {
        return new static('A project_id is required for FCM');
    }
}
