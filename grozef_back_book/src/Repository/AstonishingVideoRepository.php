<?php

namespace App\Repository;

use App\Entity\AstonishingVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository for AstonishingVideo entity.
 */
class AstonishingVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AstonishingVideo::class);
    }

    /**
     * Find videos with pagination and optional search term.
     *
     * @param int $page The page number.
     * @param int $limit The number of items per page.
     * @param string|null $searchTerm The term to search for.
     * @return Paginator The paginator object containing the results.
     */
    public function findWithPaginationAndSearch(int $page, int $limit, ?string $searchTerm = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($searchTerm) {
            $queryBuilder
                ->where('a.title LIKE :searchTerm')
                ->orWhere('a.authorFirstName LIKE :searchTerm')
                ->orWhere('a.authorLastName LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery());
    }
}
