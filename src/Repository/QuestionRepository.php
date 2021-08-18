<?php

namespace App\Repository;

use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Survey;
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

	public function findBySurveyWithOptions(Survey $survey): array
	{
		return $this->createQueryBuilder('q')
			->select('q', 'o')
			->from(Question::class, 't')
			->leftJoin('q.options', 'o')
			->where('q.survey = :survey')
			->setParameter('survey', $survey)
			->orderBy('q.ordering', 'ASC')
			->addOrderBy('o.ordering', 'ASC')
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
