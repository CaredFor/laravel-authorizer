<?php

return [
    /*
     * Cache Expiration
     * Permissions and Roles will be cached by default for 24 hours for
     * quicker retrieval.
     */
    'cache_expiration' => 1440, // 60m * 24h

    /*
     * Set the default auth guard you wish to use.
     */
    'default_guard' => 'web',

    'tables' => [
        /*
         * If you want to use UUID's instead of INTs for model ID's, set this to true.
         */
        'uses_uuid' => true,
        /*
         * The name of the table to retrieve your roles.
         */
        'roles' => 'roles',
        /*
         * The name of the table to retrieve your permissions.
         */
        'permissions' => 'permissions',
        /*
         * The name of the table to retrieve your role/model associations.
         */
        'roles_assigned' => 'roles_assigned',
        /*
         * The name of the table to retrieve your permission/model associations.
         */
        'permissions_assigned' => 'permissions_assigned',
    ],

    'teams' => [
        /*
         * Enable teams to allow users to have different roles for different teams.
         */
        'enabled' => true,
        /**
         * The default model for teams.
         */
        'model' => '\App\Team'
    ]
];