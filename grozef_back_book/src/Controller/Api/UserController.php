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

/**
 * Controller for managing User entities.
 */
#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
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

        $user = new User();
        $user->setMail($data['mail']);
        $user->setPassword($data['password']);
        $user->setIsActive($data['isActive']);

        $userInfo = new UserInfo();
        $userInfo->setFirstName($data['firstName']);
        $userInfo->setLastName($data['lastName']);
        $userInfo->setAddress($data['address']);
        $userInfo->setTel($data['tel']);
        $userInfo->setPostalCode($data['postalCode']);
        $userInfo->setCountry($data['country']);

        $user->setUserInfo($userInfo);

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
                'mail' => $user->getMail(),
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

        return new JsonResponse($data);
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
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $user->setMail($data['mail']);
        $user->setPassword($data['password']);
        $user->setIsActive($data['isActive']);

        $userInfo = $user->getUserInfo();
        $userInfo->setFirstName($data['firstName']);
        $userInfo->setLastName($data['lastName']);
        $userInfo->setAddress($data['address']);
        $userInfo->setTel($data['tel']);
        $userInfo->setPostalCode($data['postalCode']);
        $userInfo->setCountry($data['country']);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User updated!']);
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

        return new JsonResponse(['status' => 'User deleted!']);
    }
}
