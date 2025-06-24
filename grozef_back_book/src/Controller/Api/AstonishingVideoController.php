<?php

namespace App\Controller\Api;

use App\Entity\AstonishingVideo;
use App\Repository\AstonishingVideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing AstonishingVideo entities.
 */
#[Route('/api/astonishing_videos', name: 'api_astonishing_videos_')]
class AstonishingVideoController extends AbstractController
{
    private AstonishingVideoRepository $astonishingVideoRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(AstonishingVideoRepository $astonishingVideoRepository, EntityManagerInterface $entityManager)
    {
        $this->astonishingVideoRepository = $astonishingVideoRepository;
        $this->entityManager = $entityManager;
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

        $video = new AstonishingVideo();
        $video->setTitle($data['title']);
        $video->setAuthorFirstName($data['authorFirstName']);
        $video->setAuthorLastName($data['authorLastName']);
        $video->setRating($data['rating']);
        $video->setIsPublic($data['isPublic']);
        $video->setPublishDate(new \DateTime($data['publishDate']));
        $video->setPublisher($data['publisher']);
        $video->setFilepath($data['filepath']);

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
            ];
        }

        return new JsonResponse($data);
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
        $video = $this->entityManager->getRepository(AstonishingVideo::class)->find($id);

        if (!$video) {
            return new JsonResponse(['error' => 'Video not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $video->setTitle($data['title']);
        $video->setAuthorFirstName($data['authorFirstName']);
        $video->setAuthorLastName($data['authorLastName']);
        $video->setRating($data['rating']);
        $video->setIsPublic($data['isPublic']);
        $video->setPublishDate(new \DateTime($data['publishDate']));
        $video->setPublisher($data['publisher']);
        $video->setFilepath($data['filepath']);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Video updated!']);
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

        return new JsonResponse(['status' => 'Video deleted!']);
    }
}
