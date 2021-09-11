<?php

namespace App\Repository;

use App\Entity\ExternalPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExternalPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalPerson[]    findAll()
 * @method ExternalPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalPerson::class);
    }
}
