<?php

namespace App\Exceptions;

use Exception;

class BeltTestAccessException extends Exception
{
    public static function notAssigned(): self
    {
        return new self('You are not assigned to this belt test.');
    }

    public static function inactiveStudent(): self
    {
        return new self('Your student account is inactive.');
    }

    public static function testUnavailable(): self
    {
        return new self('This belt test is not available for applications.');
    }

    public static function notOpenYet(): self
    {
        return new self('Applications for this belt test have not opened yet.');
    }

    public static function closed(): self
    {
        return new self('Applications closed 30 minutes before the test (or at the configured deadline).');
    }

    public static function alreadySubmitted(): self
    {
        return new self('You have already submitted an application for this belt test.');
    }
}
