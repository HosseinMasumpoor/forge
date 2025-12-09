<?php

namespace Modules\User\Exceptions;

use Exception;

class UserException extends Exception
{
    public static function invalidCredentials() : self{
        return new self(__('user::messages.user.invalid_password'), 401);
    }

    public static function invalidOTPCode() : self{
        return new self(__('user::messages.otp_invalid'), 401);
    }
}
