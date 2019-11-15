<?php

use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Authenticatable;

function lareroot_socket_io_token(Authenticatable $user) {

    $privateKey = file_get_contents(config('lareroot-socket-io.encryption_key_path'));

    $payload = [
        'data' => ['id' => $user->getKey()],
        "iat" => 1531498466,
        "eat" => 1557000000
    ];

    return JWT::encode($payload, $privateKey, 'RS256');
}