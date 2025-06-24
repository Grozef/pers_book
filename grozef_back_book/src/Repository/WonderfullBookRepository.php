<?php

namespace App\Repository;

use App\Entity\WonderfullBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository for WonderfullBook entity.
 */
class WonderfullBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WonderfullBook::class);
    }

    /**
     * Find books with pagination and optional search term.
     *
     * @param int $page The page number.
     * @param int $limit The number of items per page.
     * @param string|null $searchTerm The term to search for.
     * @return Paginator The paginator object containing the results.
     */
    public function findWithPaginationAndSearch(int $page, int $limit, ?string $searchTerm = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('w');

        if ($searchTerm) {
            $queryBuilder
                ->where('w.title LIKE :searchTerm')
                ->orWhere('w.authorFirstName LIKE :searchTerm')
                ->orWhere('w.authorLastName LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery());
    }
}
