<?php

namespace App\Controller\Api;

use App\Entity\StunningImage;
use App\Entity\FiercePublisher;
use App\Repository\StunningImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for managing StunningImage entities.
 */
#[Route('/api/stunning_images', name: 'api_stunning_images_')]
class StunningImageController extends AbstractController
{
    private StunningImageRepository $stunningImageRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        StunningImageRepository $stunningImageRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->stunningImageRepository = $stunningImageRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Create a new StunningImage entity.
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

        $image = new StunningImage();
        $image->setTitle($data['title'] ?? '');
        $image->setAuthorFirstName($data['authorFirstName'] ?? '');
        $image->setAuthorLastName($data['authorLastName'] ?? '');
        $image->setRating(isset($data['rating']) ? (int)$data['rating'] : null);
        $image->setIsPublic($data['isPublic'] ?? false);
        $image->setPrice(isset($data['price']) ? (float)$data['price'] : null);
        try {
            $image->setPublishedDate(isset($data['publishedDate']) ? new \DateTime($data['publishedDate']) : null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid published date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $image->setPublisher($data['publisher'] ?? null);
        $image->setFilepath($data['filepath'] ?? null);

        // Associer des éditeurs
        if (!empty($data['publisherIds']) && is_array($data['publisherIds'])) {
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $image->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($image);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Image created!', 'image' => $image->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * List StunningImage entities with optional search and pagination.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response containing the images.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $searchTerm = $request->query->get('search');

        $paginator = $this->stunningImageRepository->findWithPaginationAndSearch($page, $limit, $searchTerm);

        $data = [];
        foreach ($paginator as $image) {
            $data[] = [
                'id' => $image->getId(),
                'title' => $image->getTitle(),
                'authorFirstName' => $image->getAuthorFirstName(),
                'authorLastName' => $image->getAuthorLastName(),
                'rating' => $image->getRating(),
                'isPublic' => $image->isPublic(),
                'price' => $image->getPrice(),
                'publishedDate' => $image->getPublishedDate() ? $image->getPublishedDate()->format('Y-m-d') : null,
                'publisher' => $image->getPublisher(),
                'filepath' => $image->getFilepath(),
                'publishers' => array_map(fn($publisher) => $publisher->getId(), $image->getFiercePublishers()->toArray()),
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
     * Update an existing StunningImage entity.
     *
     * @param Request $request The HTTP request.
     * @param int $id The ID of the image to update.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $image = $this->entityManager->getRepository(StunningImage::class)->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $image->setTitle($data['title'] ?? $image->getTitle());
        $image->setAuthorFirstName($data['authorFirstName'] ?? $image->getAuthorFirstName());
        $image->setAuthorLastName($data['authorLastName'] ?? $image->getAuthorLastName());
        $image->setRating(isset($data['rating']) ? (int)$data['rating'] : $image->getRating());
        $image->setIsPublic($data['isPublic'] ?? $image->isPublic());
        $image->setPrice(isset($data['price']) ? (float)$data['price'] : $image->getPrice());
        try {
            $image->setPublishedDate(isset($data['publishedDate']) ? new \DateTime($data['publishedDate']) : $image->getPublishedDate());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid published date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $image->setPublisher($data['publisher'] ?? $image->getPublisher());
        $image->setFilepath($data['filepath'] ?? $image->getFilepath());

        // Mettre à jour les éditeurs
        if (isset($data['publisherIds']) && is_array($data['publisherIds'])) {
            foreach ($image->getFiercePublishers() as $publisher) {
                $image->removeFiercePublisher($publisher);
            }
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $image->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($image);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Image updated!'], JsonResponse::HTTP_OK);
    }

    /**
     * Delete a StunningImage entity.
     *
     * @param int $id The ID of the image to delete.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $image = $this->entityManager->getRepository(StunningImage::class)->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Image deleted!'], JsonResponse::HTTP_OK);
    }
}