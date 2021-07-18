<?php

namespace App\Repository;

use App\Entity\Pass;
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

	public function findByPassWithOptionsAndAnswers(Pass $pass): array
	{
		return $this->createQueryBuilder('q')
			->select('q', 'o', 'a')
			->from(Question::class, 't')
			->join('q.options', 'o')
			->join('q.answers', 'a', indexBy: 'a.option')
			->where('a.pass = :id')
			->orderBy('o.ordering')
			->addOrderBy('q.ordering')
			->setParameter('id', $pass)
			->getQuery()
			->getResult();
    }
}
