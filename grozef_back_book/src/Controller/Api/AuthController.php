<?php
// src/Controller/Api/AuthController.php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Exemple de vérification simple (à remplacer par un vrai système d'authentification)
        if ($email === 'test@example.com' && $password === 'secret') {
            return new JsonResponse(['success' => true, 'token' => 'fake-jwt-token']);
        }

        return new JsonResponse(['success' => false, 'error' => 'Identifiants invalides'], 401);
    }
}
