<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'amocrm' => [
        'description'   => 'Подключите платформу к вашему аккаунту для подключения интеграций',
        'redirect_uri'  => env('AMOCRM_REDIRECT'),
        'client_secret' => env('AMOCRM_SECRET'),
        'client_id'     => env('AMOCRM_CLIENT_ID'),
    ],

    'telegram' => [
        'token'   => env('TG_TOKEN_MY'),
        'chat_id' => env('TG_CHAT_ID_MY'),
    ],

    'getcourse' => [
        'wh_form_params'  => '?email={object.email}&phone={object.phone}&name={object.name}',
        'wh_order_params' => '?phone={object.user.phone}&name={object.user.first_name}&email={object.user.email}&number={object.number}&id={object.id}&positions={object.positions}&left_cost_money={object.left_cost_money}&cost_money={object.cost_money}&payed_money={object.payed_money}&status={object.status}&link={object.payment_link}',
    ]
];
