<?php

return [
    'name' => 'Order',
    'success_payment_redirect_url' => env('SUCCESS_PAYMENT_REDIRECT_URL', 'http://localhost:5173?status=success'),
    'failure_payment_redirect_url' => env('FAILURE_PAYMENT_REDIRECT_URL', 'http://localhost:5173?status=failure'),
];
