<?php

class Auth {
    private const COOKIE_NAME = 'metrix_auth_token';
    private const TOKEN_TTL_SECONDS = 28800; // 8h

    private static function getJwtSecret() {
        $secret = getenv('JWT_SECRET');
        if ($secret !== false && trim($secret) !== '') {
            return trim($secret);
        }

        return 'change-this-secret-in-production';
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        $padding = strlen($data) % 4;
        if ($padding > 0) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function getTokenFromCookieOrHeader() {
        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            return $_COOKIE[self::COOKIE_NAME];
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private static function encodeJwt($payload) {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::getJwtSecret(), true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    private static function decodeJwt($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        $headerJson = self::base64UrlDecode($headerEncoded);
        $payloadJson = self::base64UrlDecode($payloadEncoded);
        if ($headerJson === false || $payloadJson === false) {
            return null;
        }

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);
        if (!is_array($header) || !is_array($payload)) {
            return null;
        }

        if (($header['alg'] ?? '') !== 'HS256') {
            return null;
        }

        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::getJwtSecret(), true)
        );

        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            return null;
        }

        $now = time();
        $exp = isset($payload['exp']) ? intval($payload['exp']) : 0;
        $nbf = isset($payload['nbf']) ? intval($payload['nbf']) : 0;
        if ($exp <= 0 || $now >= $exp || ($nbf > 0 && $now < $nbf)) {
            return null;
        }

        return $payload;
    }

    private static function setAuthCookie($token, $expiresAt) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie(self::COOKIE_NAME, $token, [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    public static function login($user) {
        $now = time();
        $exp = $now + self::TOKEN_TTL_SECONDS;

        $payload = [
            'sub' => intval($user['id']),
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp
        ];

        $token = self::encodeJwt($payload);
        self::setAuthCookie($token, $exp);
    }

    public static function logout() {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie(self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    public static function user() {
        $token = self::getTokenFromCookieOrHeader();
        if (!$token) {
            return null;
        }

        $payload = self::decodeJwt($token);
        if (!$payload) {
            return null;
        }

        return [
            'id' => intval($payload['sub']),
            'email' => $payload['email'] ?? '',
            'role' => $payload['role'] ?? 'user'
        ];
    }

    public static function requirePageAuth($loginPath = 'login.php') {
        $user = self::user();
        if ($user) {
            return $user;
        }

        header('Location: ' . $loginPath);
        exit;
    }

    public static function requireApiAuth() {
        $user = self::user();
        if ($user) {
            return $user;
        }

        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Non authentifie'
        ]);
        exit;
    }

    public static function requirePageRole($loginPath, $forbiddenRedirectPath, $allowedRoles) {
        $user = self::requirePageAuth($loginPath);
        $role = $user['role'] ?? 'user';

        if (in_array($role, $allowedRoles, true)) {
            return $user;
        }

        header('Location: ' . $forbiddenRedirectPath);
        exit;
    }

    public static function requireApiRole($allowedRoles) {
        $user = self::requireApiAuth();
        $role = $user['role'] ?? 'user';

        if (in_array($role, $allowedRoles, true)) {
            return $user;
        }

        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Acces refuse'
        ]);
        exit;
    }
}

?>
