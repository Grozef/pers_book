<?php

namespace App\Controller\Api;

use App\Entity\FiercePublisher;
use App\Entity\AstonishingVideo;
use App\Entity\StunningImage;
use App\Entity\WonderfullBook;
use App\Repository\FiercePublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for managing FiercePublisher entities.
 */
#[Route('/api/fierce_publishers', name: 'api_fierce_publishers_')]
class FiercePublisherController extends AbstractController
{
    private FiercePublisherRepository $fiercePublisherRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        FiercePublisherRepository $fiercePublisherRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->fiercePublisherRepository = $fiercePublisherRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
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

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $publisher = new FiercePublisher();
        $publisher->setName($data['name'] ?? '');
        $publisher->setAddress($data['address'] ?? null);
        $publisher->setTel($data['tel'] ?? null);
        $publisher->setEmail($data['email'] ?? null);
        $publisher->setPostalCode($data['postalCode'] ?? null);
        $publisher->setCountry($data['country'] ?? null);

        // Associer des vidéos, images et livres
        if (!empty($data['videoIds']) && is_array($data['videoIds'])) {
            foreach ($data['videoIds'] as $videoId) {
                $video = $this->entityManager->getRepository(AstonishingVideo::class)->find($videoId);
                if ($video) {
                    $publisher->addAstonishingVideo($video);
                } else {
                    return new JsonResponse(['error' => "Video ID $videoId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }
        if (!empty($data['imageIds']) && is_array($data['imageIds'])) {
            foreach ($data['imageIds'] as $imageId) {
                $image = $this->entityManager->getRepository(StunningImage::class)->find($imageId);
                if ($image) {
                    $publisher->addStunningImage($image);
                } else {
                    return new JsonResponse(['error' => "Image ID $imageId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }
        if (!empty($data['bookIds']) && is_array($data['bookIds'])) {
            foreach ($data['bookIds'] as $bookId) {
                $book = $this->entityManager->getRepository(WonderfullBook::class)->find($bookId);
                if ($book) {
                    $publisher->addWonderfullBook($book);
                } else {
                    return new JsonResponse(['error' => "Book ID $bookId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($publisher);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

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
                'email' => $publisher->getEmail(),
                'postalCode' => $publisher->getPostalCode(),
                'country' => $publisher->getCountry(),
                'videos' => array_map(fn($video) => $video->getId(), $publisher->getAstonishingVideos()->toArray()),
                'images' => array_map(fn($image) => $image->getId(), $publisher->getStunningImages()->toArray()),
                'books' => array_map(fn($book) => $book->getId(), $publisher->getWonderfullBooks()->toArray()),
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

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($id);

        if (!$publisher) {
            return new JsonResponse(['error' => 'Publisher not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $publisher->setName($data['name'] ?? $publisher->getName());
        $publisher->setAddress($data['address'] ?? $publisher->getAddress());
        $publisher->setTel($data['tel'] ?? $publisher->getTel());
        $publisher->setEmail($data['email'] ?? $publisher->getEmail());
        $publisher->setPostalCode($data['postalCode'] ?? $publisher->getPostalCode());
        $publisher->setCountry($data['country'] ?? $publisher->getCountry());

        // Mettre à jour les relations
        if (isset($data['videoIds']) && is_array($data['videoIds'])) {
            foreach ($publisher->getAstonishingVideos() as $video) {
                $publisher->removeAstonishingVideo($video);
            }
            foreach ($data['videoIds'] as $videoId) {
                $video = $this->entityManager->getRepository(AstonishingVideo::class)->find($videoId);
                if ($video) {
                    $publisher->addAstonishingVideo($video);
                } else {
                    return new JsonResponse(['error' => "Video ID $videoId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }
        if (isset($data['imageIds']) && is_array($data['imageIds'])) {
            foreach ($publisher->getStunningImages() as $image) {
                $publisher->removeStunningImage($image);
            }
            foreach ($data['imageIds'] as $imageId) {
                $image = $this->entityManager->getRepository(StunningImage::class)->find($imageId);
                if ($image) {
                    $publisher->addStunningImage($image);
                } else {
                    return new JsonResponse(['error' => "Image ID $imageId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }
        if (isset($data['bookIds']) && is_array($data['bookIds'])) {
            foreach ($publisher->getWonderfullBooks() as $book) {
                $publisher->removeWonderfullBook($book);
            }
            foreach ($data['bookIds'] as $bookId) {
                $book = $this->entityManager->getRepository(WonderfullBook::class)->find($bookId);
                if ($book) {
                    $publisher->addWonderfullBook($book);
                } else {
                    return new JsonResponse(['error' => "Book ID $bookId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($publisher);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Publisher updated!'], JsonResponse::HTTP_OK);
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

        return new JsonResponse(['status' => 'Publisher deleted!'], JsonResponse::HTTP_OK);
    }
}