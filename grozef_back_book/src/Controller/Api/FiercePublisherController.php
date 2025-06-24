<?php

namespace App\Controller\Api;

use App\Entity\FiercePublisher;
use App\Repository\FiercePublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing FiercePublisher entities.
 */
#[Route('/api/fierce_publishers', name: 'api_fierce_publishers_')]
class FiercePublisherController extends AbstractController
{
    private FiercePublisherRepository $fiercePublisherRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(FiercePublisherRepository $fiercePublisherRepository, EntityManagerInterface $entityManager)
    {
        $this->fiercePublisherRepository = $fiercePublisherRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Create a new FiercePublisher entity.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $publisher = new FiercePublisher();
        $publisher->setName($data['name']);
        $publisher->setAddress($data['address']);
        $publisher->setTel($data['tel']);
        $publisher->setMail($data['mail']);
        $publisher->setPostalCode($data['postalCode']);
        $publisher->setCountry($data['country']);

        $this->entityManager->persist($publisher);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Publisher created!', 'publisher' => $publisher->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * List FiercePublisher entities with optional search and pagination.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response containing the publishers.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $searchTerm = $request->query->get('search');

        $paginator = $this->fiercePublisherRepository->findWithPaginationAndSearch($page, $limit, $searchTerm);

        $data = [];
        foreach ($paginator as $publisher) {
            $data[] = [
                'id' => $publisher->getId(),
                'name' => $publisher->getName(),
                'address' => $publisher->getAddress(),
                'tel' => $publisher->getTel(),
                'mail' => $publisher->getMail(),
                'postalCode' => $publisher->getPostalCode(),
                'country' => $publisher->getCountry(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Update an existing FiercePublisher entity.
     *
     * @param Request $request The HTTP request.
     * @param int $id The ID of the publisher to update.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($id);

        if (!$publisher) {
            return new JsonResponse(['error' => 'Publisher not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $publisher->setName($data['name']);
        $publisher->setAddress($data['address']);
        $publisher->setTel($data['tel']);
        $publisher->setMail($data['mail']);
        $publisher->setPostalCode($data['postalCode']);
        $publisher->setCountry($data['country']);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Publisher updated!']);
    }

    /**
     * Delete a FiercePublisher entity.
     *
     * @param int $id The ID of the publisher to delete.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($id);

        if (!$publisher) {
            return new JsonResponse(['error' => 'Publisher not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($publisher);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Publisher deleted!']);
    }
}
