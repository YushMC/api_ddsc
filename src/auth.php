<?php


use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Auth {

    private static $jwtSecret;

    public static function init() {

        self::$jwtSecret = getenv('JWT_SECRET');

        // Verificar que la clave no sea NULL
        if (empty(self::$jwtSecret)) {
            throw new Exception("Error: JWT_SECRET no está configurado correctamente.");
        }


    }

    public static function generarToken($datos) {
        $payload = [
            "iss" => "ddsc_users_api",
            "aud" => "users",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24 * 3), // Expira en 3 días
            "data" => $datos
        ];
        return JWT::encode($payload, self:: $jwtSecret, 'HS256');
    }

    public static function verificarToken($token) {
        try {
            return JWT::decode($token, new Key(self:: $jwtSecret, 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }
}