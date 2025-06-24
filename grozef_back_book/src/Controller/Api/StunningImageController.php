<?php

namespace App\Controller\Api;

use App\Entity\StunningImage;
use App\Repository\StunningImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing StunningImage entities.
 */
#[Route('/api/stunning_images', name: 'api_stunning_images_')]
class StunningImageController extends AbstractController
{
    private StunningImageRepository $stunningImageRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(StunningImageRepository $stunningImageRepository, EntityManagerInterface $entityManager)
    {
        $this->stunningImageRepository = $stunningImageRepository;
        $this->entityManager = $entityManager;
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

        $image = new StunningImage();
        $image->setTitle($data['title']);
        $image->setAuthorFirstName($data['authorFirstName']);
        $image->setAuthorLastName($data['authorLastName']);
        $image->setRating($data['rating']);
        $image->setIsPublic($data['isPublic']);
        $image->setPrice($data['price']);
        $image->setPublishedDate(new \DateTime($data['publishedDate']));
        $image->setPublisher($data['publisher']);
        $image->setFilepath($data['filepath']);

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
            ];
        }

        return new JsonResponse($data);
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
        $image = $this->entityManager->getRepository(StunningImage::class)->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $image->setTitle($data['title']);
        $image->setAuthorFirstName($data['authorFirstName']);
        $image->setAuthorLastName($data['authorLastName']);
        $image->setRating($data['rating']);
        $image->setIsPublic($data['isPublic']);
        $image->setPrice($data['price']);
        $image->setPublishedDate(new \DateTime($data['publishedDate']));
        $image->setPublisher($data['publisher']);
        $image->setFilepath($data['filepath']);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Image updated!']);
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

        return new JsonResponse(['status' => 'Image deleted!']);
    }
}
