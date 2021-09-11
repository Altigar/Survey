<?php

namespace App\Repository;

use App\Entity\Pass;
use App\Entity\Person;
use App\Entity\Survey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pass|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pass|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pass[]    findAll()
 * @method Pass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pass::class);
    }

	public function findByPersonOrIp(Survey $survey, ?Person $person, string $ip)
	{
		return $this->createQueryBuilder('p')
			->select('p', 'ep')
			->from(Pass::class, 't')
			->join('p.externalPerson', 'ep')
			->where('p.person = :person')
			->orWhere('ep.ip = :ip')
			->andWhere('p.survey = :survey')
			->setParameter('person', $person)
			->setParameter('ip', $ip)
			->setParameter('survey', $survey)
			->getQuery()
			->getResult();
    }
}
