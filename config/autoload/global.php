<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

$dbconfGlobal = parse_url(getenv("DATABASE_URL"));

return [
    'db' => [
        'driver'   => 'pdo_pgsql',
        'database' => isset($dbconfGlobal['path']) ? ltrim($dbconfGlobal["path"], "/") : null,
        'username' => isset($dbconfGlobal['user']) ? $dbconfGlobal['user'] : null,
        'password' => isset($dbconfGlobal['pass']) ? $dbconfGlobal['pass'] : null,
        'hostname' => isset($dbconfGlobal['host']) ? $dbconfGlobal['host'] : null,
        'port'     => isset($dbconfGlobal['port']) ? $dbconfGlobal['port'] : null,
    ],
];
