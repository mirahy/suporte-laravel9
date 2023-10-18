<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default LDAP Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the LDAP connections below you wish
    | to use as your default connection for all LDAP operations. Of
    | course you may add as many connections you'd like below.
    |
    */

    'default' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    |
    | Below you may configure each LDAP connection your application requires
    | access to. Be sure to include a valid base DN - otherwise you may
    | not receive any results when performing LDAP search operations.
    |
    */

    'connections' => [

        'default' => [
            'hosts' => [env('LDAP_DEFAULT_HOSTS', '127.0.0.1')],
            'username' => env('LDAP_DEFAULT_USERNAME', 'cn=user,dc=local,dc=com'),
            'password' => env('LDAP_DEFAULT_PASSWORD', 'secret'),
            'port' => env('LDAP_DEFAULT_PORT', 389),
            'base_dn' => env('LDAP_DEFAULT_BASE_DN', 'dc=local,dc=com'),
            'timeout' => env('LDAP_DEFAULT_TIMEOUT', 5),
            'use_ssl' => env('LDAP_DEFAULT_SSL', false),
            'use_tls' => env('LDAP_DEFAULT_TLS', false),
            'use_sasl' => env('LDAP_DEFAULT_SASL', false),
            'sasl_options' => [
                // 'mech' => 'GSSAPI',
            ],
            // Custom LDAP Options
            // 'options' => [
            //     // See: http://php.net/ldap_set_option
            //     LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER
            // ],
        ],

        // 'userKeep' => [
        //     'hosts' => [env('LDAP_USERKEEP_HOSTS', '127.0.0.1')],
        //     'username' => env('LDAP_USERKEEP_USERNAME', 'cn=user,dc=local,dc=com'),
        //     'password' => env('LDAP_USERKEEP_PASSWORD', 'secret'),
        //     'port' => env('LDAP_USERKEEP_PORT', 389),
        //     'base_dn' => env('LDAP_USERKEEP_BASE_DN', 'dc=local,dc=com'),
        //     'timeout' => env('LDAP_USERKEEP_TIMEOUT', 5),
        //     'use_ssl' => env('LDAP_USERKEEP_SSL', false),
        //     'use_tls' => env('LDAP_USERKEEP_TLS', false),
        //     'use_sasl' => env('LDAP_USERKEEP_SASL', false),
        //     'sasl_options' => [
        //         // 'mech' => 'GSSAPI',
        //     ],
        //     // Custom LDAP Options
        //     'options' => [
        //         // See: http://php.net/ldap_set_option
        //         LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER
        //     ],
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Logging
    |--------------------------------------------------------------------------
    |
    | When LDAP logging is enabled, all LDAP search and authentication
    | operations are logged using the default application logging
    | driver. This can assist in debugging issues and more.
    |
    */

    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Cache
    |--------------------------------------------------------------------------
    |
    | LDAP caching enables the ability of caching search results using the
    | query builder. This is great for running expensive operations that
    | may take many seconds to complete, such as a pagination request.
    |
    */

    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

];
