<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Firebase\JWT\JWT;
use PDO;

class AuthController {
    private $pdo;
    private $key = "your_secret_key_here"; // Move this to .env in production

    public function __construct() {
        // Simple PDO connection
        $this->pdo = new PDO('mysql:host=mysql;port=3306;dbname=php_framework_app', 'homestead', 'secret');
    }

    public function register(Request $request): Response {
        $params = json_decode($request->getContent(), true);
        $hashedPassword = password_hash($params['password'], PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        try {
            $stmt->execute([$params['email'], $hashedPassword]);
            return new Response(json_encode(['status' => 'User created']), 201);
        } catch (\PDOException $e) {
            return new Response(json_encode(['error' => 'Email already exists']), 400);
        }
    }

    public function login(Request $request): Response {
        $params = json_decode($request->getContent(), true);
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$params['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($params['password'], $user['password'])) {
            return new Response(json_encode(['error' => 'Unauthorized']), 401);
        }

        $payload = ['id' => $user['id'], 'exp' => time() + 3600];
        $jwt = JWT::encode($payload, $this->key, 'HS256');

        $response = new Response(json_encode(['message' => 'Logged in']));
        
        // HttpOnly Cookie prevents XSS access
        $cookie = Cookie::create('access_token')
            ->withValue($jwt)
            ->withHttpOnly(true)
            ->withSecure(false) // Set TRUE for production (HTTPS)
            ->withSameSite('Lax');

        $response->headers->setCookie($cookie);
        return $response;
    }
}