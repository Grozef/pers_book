<?php

namespace App\Controller\Api;

use App\Entity\AstonishingVideo;
use App\Entity\FiercePublisher;
use App\Repository\AstonishingVideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for managing AstonishingVideo entities.
 */
#[Route('/api/astonishing_videos', name: 'api_astonishing_videos_')]
class AstonishingVideoController extends AbstractController
{
    private AstonishingVideoRepository $astonishingVideoRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        AstonishingVideoRepository $astonishingVideoRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->astonishingVideoRepository = $astonishingVideoRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Create a new AstonishingVideo entity.
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

        $video = new AstonishingVideo();
        $video->setTitle($data['title'] ?? '');
        $video->setAuthorFirstName($data['authorFirstName'] ?? '');
        $video->setAuthorLastName($data['authorLastName'] ?? '');
        $video->setRating(isset($data['rating']) ? (int)$data['rating'] : null);
        $video->setIsPublic($data['isPublic'] ?? false);
        try {
            $video->setPublishDate(isset($data['publishDate']) ? new \DateTime($data['publishDate']) : null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid publish date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $video->setPublisher($data['publisher'] ?? null);
        $video->setFilepath($data['filepath'] ?? null);

        // Associer des éditeurs
        if (!empty($data['publisherIds']) && is_array($data['publisherIds'])) {
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $video->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($video);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($video);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Video created!', 'video' => $video->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * List AstonishingVideo entities with optional search and pagination.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response containing the videos.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $searchTerm = $request->query->get('search');

        $paginator = $this->astonishingVideoRepository->findWithPaginationAndSearch($page, $limit, $searchTerm);

        $data = [];
        foreach ($paginator as $video) {
            $data[] = [
                'id' => $video->getId(),
                'title' => $video->getTitle(),
                'authorFirstName' => $video->getAuthorFirstName(),
                'authorLastName' => $video->getAuthorLastName(),
                'rating' => $video->getRating(),
                'isPublic' => $video->isPublic(),
                'publishDate' => $video->getPublishDate() ? $video->getPublishDate()->format('Y-m-d') : null,
                'publisher' => $video->getPublisher(),
                'filepath' => $video->getFilepath(),
                'publishers' => array_map(fn($publisher) => $publisher->getId(), $video->getFiercePublishers()->toArray()),
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
     * Update an existing AstonishingVideo entity.
     *
     * @param Request $request The HTTP request.
     * @param int $id The ID of the video to update.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $video = $this->entityManager->getRepository(AstonishingVideo::class)->find($id);

        if (!$video) {
            return new JsonResponse(['error' => 'Video not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $video->setTitle($data['title'] ?? $video->getTitle());
        $video->setAuthorFirstName($data['authorFirstName'] ?? $video->getAuthorFirstName());
        $video->setAuthorLastName($data['authorLastName'] ?? $video->getAuthorLastName());
        $video->setRating(isset($data['rating']) ? (int)$data['rating'] : $video->getRating());
        $video->setIsPublic($data['isPublic'] ?? $video->isPublic());
        try {
            $video->setPublishDate(isset($data['publishDate']) ? new \DateTime($data['publishDate']) : $video->getPublishDate());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid publish date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $video->setPublisher($data['publisher'] ?? $video->getPublisher());
        $video->setFilepath($data['filepath'] ?? $video->getFilepath());

        // Mettre à jour les éditeurs
        if (isset($data['publisherIds']) && is_array($data['publisherIds'])) {
            // Supprimer les éditeurs existants
            foreach ($video->getFiercePublishers() as $publisher) {
                $video->removeFiercePublisher($publisher);
            }
            // Ajouter les nouveaux éditeurs
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $video->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($video);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Video updated!'], JsonResponse::HTTP_OK);
    }

    /**
     * Delete an AstonishingVideo entity.
     *
     * @param int $id The ID of the video to delete.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $video = $this->entityManager->getRepository(AstonishingVideo::class)->find($id);

        if (!$video) {
            return new JsonResponse(['error' => 'Video not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($video);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Video deleted!'], JsonResponse::HTTP_OK);
    }
}