<?php

if (!function_exists('convertToValidMobileNumber')) {
    function convertToValidMobileNumber($mobileNumber){
        return "+98" . substr($mobileNumber, -10, 10);
    }
}
