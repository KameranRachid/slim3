<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class Recaptcha extends AbstractRule
{

    public function validate($responseKey)
    {
        $secretKey = "6LcZCjYUAAAAAOSJZ93KRxN9T0VzopzyCBI9f90h";
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey";
        $response = json_decode(file_get_contents($url));

        return $response->success;
    }

}