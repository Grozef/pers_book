<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Controller for managing User entities.
 */
#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Create a new User entity.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $userInfo = new UserInfo();
        $userInfo->setFirstName($data['firstName'] ?? '');
        $userInfo->setLastName($data['lastName'] ?? '');
        $userInfo->setAddress($data['address'] ?? null);
        $userInfo->setTel($data['tel'] ?? null);
        $userInfo->setPostalCode($data['postalCode'] ?? null);
        $userInfo->setCountry($data['country'] ?? null);

        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setPassword(isset($data['password']) ? $this->passwordHasher->hashPassword($user, $data['password']) : '');
        $user->setIsActive($data['isActive'] ?? false);
        $user->setUserInfo($userInfo);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($userInfo);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($userInfo);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User created!', 'user' => $user->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * List User entities with optional search and pagination.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response containing the users.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $searchTerm = $request->query->get('search');

        $paginator = $this->userRepository->findWithPaginationAndSearch($page, $limit, $searchTerm);

        $data = [];
        foreach ($paginator as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'isActive' => $user->isActive(),
                'userInfo' => [
                    'firstName' => $user->getUserInfo()->getFirstName(),
                    'lastName' => $user->getUserInfo()->getLastName(),
                    'address' => $user->getUserInfo()->getAddress(),
                    'tel' => $user->getUserInfo()->getTel(),
                    'postalCode' => $user->getUserInfo()->getPostalCode(),
                    'country' => $user->getUserInfo()->getCountry(),
                ],
            ];
        }

        return new JsonResponse([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($paginator->count() / $limit),
                'total_items' => $paginator->count(),
                'items_per_page' => $limit,
            ],
        ]);
    }

    /**
     * Update an existing User entity.
     *
     * @param Request $request The HTTP request.
     * @param int $id The ID of the user to update.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $user->setEmail($data['email'] ?? $user->getEmail());
        if (isset($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }
        $user->setIsActive($data['isActive'] ?? $user->isActive());

        $userInfo = $user->getUserInfo();
        $userInfo->setFirstName($data['firstName'] ?? $userInfo->getFirstName());
        $userInfo->setLastName($data['lastName'] ?? $userInfo->getLastName());
        $userInfo->setAddress($data['address'] ?? $userInfo->getAddress());
        $userInfo->setTel($data['tel'] ?? $userInfo->getTel());
        $userInfo->setPostalCode($data['postalCode'] ?? $userInfo->getPostalCode());
        $userInfo->setCountry($data['country'] ?? $userInfo->getCountry());

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($userInfo);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User updated!'], JsonResponse::HTTP_OK);
    }

    /**
     * Delete a User entity.
     *
     * @param int $id The ID of the user to delete.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User deleted!'], JsonResponse::HTTP_OK);
    }
}