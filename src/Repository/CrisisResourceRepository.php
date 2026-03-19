<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CrisisResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CrisisResource>
 */
final class CrisisResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CrisisResource::class);
    }

    /** @return CrisisResource[] */
    public function findByCountryCode(string $countryCode): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.country', 'c')
            ->where('c.code = :code')
            ->setParameter('code', $countryCode)
            ->orderBy('r.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPrimaryForCountry(string $countryCode): ?CrisisResource
    {
        return $this->createQueryBuilder('r')
            ->join('r.country', 'c')
            ->where('c.code = :code')
            ->andWhere('r.isPrimary = true')
            ->setParameter('code', $countryCode)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
