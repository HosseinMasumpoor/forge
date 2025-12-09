<?php

namespace Modules\User\Exceptions;

use Exception;

class AuthException extends Exception
{
    public static function invalidCredentials() : self{
        return new self(__('user::messages.invalid_credentials'), 401);
    }

    public static function otpAlreadySent() : self{
        return new self(__('user::messages.otp_already_sent'), 403);
    }

    public static function otpExpired() : self{
        return new self(__('user::messages.otp_expired'), 403);
    }

    public static function otpInvalid() : self{
        return new self(__('user::messages.otp_invalid'), 403);
    }

}
