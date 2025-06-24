<?php

namespace App\Controller\Api;

use App\Entity\WonderfullBook;
use App\Repository\WonderfullBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing WonderfullBook entities.
 */
#[Route('/api/wonderfull_books', name: 'api_wonderfull_books_')]
class WonderfullBookController extends AbstractController
{
    private WonderfullBookRepository $wonderfullBookRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(WonderfullBookRepository $wonderfullBookRepository, EntityManagerInterface $entityManager)
    {
        $this->wonderfullBookRepository = $wonderfullBookRepository;
        $this->entityManager = $entityManager;
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

        $book = new WonderfullBook();
        $book->setTitle($data['title']);
        $book->setAuthorFirstName($data['authorFirstName']);
        $book->setAuthorLastName($data['authorLastName']);
        $book->setRating($data['rating']);
        $book->setIsPublic($data['isPublic']);
        $book->setPrice($data['price']);
        $book->setPublishedDate(new \DateTime($data['publishedDate']));
        $book->setGenre($data['genre']);
        $book->setPublisher($data['publisher']);
        $book->setIsbn($data['isbn']);

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
            ];
        }

        return new JsonResponse($data);
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
        $book = $this->entityManager->getRepository(WonderfullBook::class)->find($id);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $book->setTitle($data['title']);
        $book->setAuthorFirstName($data['authorFirstName']);
        $book->setAuthorLastName($data['authorLastName']);
        $book->setRating($data['rating']);
        $book->setIsPublic($data['isPublic']);
        $book->setPrice($data['price']);
        $book->setPublishedDate(new \DateTime($data['publishedDate']));
        $book->setGenre($data['genre']);
        $book->setPublisher($data['publisher']);
        $book->setIsbn($data['isbn']);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book updated!']);
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

        return new JsonResponse(['status' => 'Book deleted!']);
    }
}
