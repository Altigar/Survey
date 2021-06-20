<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

	public function findById(int $id): ?Question
	{
		return $this->createQueryBuilder('q')
			->select('q', 'o')
			->from(Question::class, 't')
			->leftJoin('q.options', 'o', null, null, 'o.id')
			->where('q.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
    }

	public function findByIdPersonWithAnswers(int $id, int $person)
	{
		return $this->createQueryBuilder('q')
			->select('q', 'o')
			->from(Question::class, 't')
			->join('q.options', 'o')
			->where('q.survey = :id')
			->setParameter('id', $id)
			->getQuery()
			->getResult();
    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
