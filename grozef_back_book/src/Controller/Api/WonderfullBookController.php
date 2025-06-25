<?php

namespace App\Controller\Api;

use App\Entity\WonderfullBook;
use App\Entity\FiercePublisher;
use App\Repository\WonderfullBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for managing WonderfullBook entities.
 */
#[Route('/api/wonderfull_books', name: 'api_wonderfull_books_')]
class WonderfullBookController extends AbstractController
{
    private WonderfullBookRepository $wonderfullBookRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        WonderfullBookRepository $wonderfullBookRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->wonderfullBookRepository = $wonderfullBookRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Create a new WonderfullBook entity.
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

        $book = new WonderfullBook();
        $book->setTitle($data['title'] ?? '');
        $book->setAuthorFirstName($data['authorFirstName'] ?? '');
        $book->setAuthorLastName($data['authorLastName'] ?? '');
        $book->setRating(isset($data['rating']) ? (int)$data['rating'] : null);
        $book->setIsPublic($data['isPublic'] ?? false);
        $book->setPrice(isset($data['price']) ? (float)$data['price'] : null);
        try {
            $book->setPublishedDate(isset($data['publishedDate']) ? new \DateTime($data['publishedDate']) : null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid published date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $book->setGenre($data['genre'] ?? null);
        $book->setPublisher($data['publisher'] ?? null);
        $book->setIsbn($data['isbn'] ?? null);

        // Associer des éditeurs
        if (!empty($data['publisherIds']) && is_array($data['publisherIds'])) {
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $book->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book created!', 'book' => $book->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * List WonderfullBook entities with optional search and pagination.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response containing the books.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $searchTerm = $request->query->get('search');

        $paginator = $this->wonderfullBookRepository->findWithPaginationAndSearch($page, $limit, $searchTerm);

        $data = [];
        foreach ($paginator as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'authorFirstName' => $book->getAuthorFirstName(),
                'authorLastName' => $book->getAuthorLastName(),
                'rating' => $book->getRating(),
                'isPublic' => $book->isPublic(),
                'price' => $book->getPrice(),
                'publishedDate' => $book->getPublishedDate() ? $book->getPublishedDate()->format('Y-m-d') : null,
                'genre' => $book->getGenre(),
                'publisher' => $book->getPublisher(),
                'isbn' => $book->getIsbn(),
                'publishers' => array_map(fn($publisher) => $publisher->getId(), $book->getFiercePublishers()->toArray()),
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
     * Update an existing WonderfullBook entity.
     *
     * @param Request $request The HTTP request.
     * @param int $id The ID of the book to update.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book = $this->entityManager->getRepository(WonderfullBook::class)->find($id);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $book->setTitle($data['title'] ?? $book->getTitle());
        $book->setAuthorFirstName($data['authorFirstName'] ?? $book->getAuthorFirstName());
        $book->setAuthorLastName($data['authorLastName'] ?? $book->getAuthorLastName());
        $book->setRating(isset($data['rating']) ? (int)$data['rating'] : $book->getRating());
        $book->setIsPublic($data['isPublic'] ?? $book->isPublic());
        $book->setPrice(isset($data['price']) ? (float)$data['price'] : $book->getPrice());
        try {
            $book->setPublishedDate(isset($data['publishedDate']) ? new \DateTime($data['publishedDate']) : $book->getPublishedDate());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid published date format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $book->setGenre($data['genre'] ?? $book->getGenre());
        $book->setPublisher($data['publisher'] ?? $book->getPublisher());
        $book->setIsbn($data['isbn'] ?? $book->getIsbn());

        // Mettre à jour les éditeurs
        if (isset($data['publisherIds']) && is_array($data['publisherIds'])) {
            foreach ($book->getFiercePublishers() as $publisher) {
                $book->removeFiercePublisher($publisher);
            }
            foreach ($data['publisherIds'] as $publisherId) {
                $publisher = $this->entityManager->getRepository(FiercePublisher::class)->find($publisherId);
                if ($publisher) {
                    $book->addFiercePublisher($publisher);
                } else {
                    return new JsonResponse(['error' => "Publisher ID $publisherId not found"], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book updated!'], JsonResponse::HTTP_OK);
    }

    /**
     * Delete a WonderfullBook entity.
     *
     * @param int $id The ID of the book to delete.
     * @return JsonResponse The JSON response.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $book = $this->entityManager->getRepository(WonderfullBook::class)->find($id);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book deleted!'], JsonResponse::HTTP_OK);
    }
}