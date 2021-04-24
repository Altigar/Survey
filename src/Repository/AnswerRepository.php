<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

	public function findNoteStatsBySurvey(int $survey): array
	{
		$entityManager = $this->getEntityManager();
		$sql = <<<SQL
			SELECT question_id, answer.text
			FROM answer
			JOIN question ON question.id = answer.question_id
			JOIN survey ON question.survey_id = survey.id
			WHERE survey_id = :survey AND (question.type = :text OR question.type = :string)
			GROUP BY question_id, answer.text
		SQL;
		$rsm = (new ResultSetMapping())
			->addScalarResult('text', 'text')
			->addScalarResult('question_id', 'question_id');
		$query = $entityManager->createNativeQuery($sql, $rsm)
			->setParameter(':survey', $survey)
			->setParameter(':text', 'text')
			->setParameter(':string', 'string');

		return $query->getResult();
	}

	public function findChoiceStatsBySurvey(int $survey): array
	{
		$entityManager = $this->getEntityManager();
		$sql = <<<SQL
			SELECT question_id, option_id, count(option_id) AS amount, sum(count(option_id)) OVER(partition by question_id) as total
			FROM answer
			JOIN question ON question.id = answer.question_id
			JOIN survey ON question.survey_id = survey.id
			WHERE survey_id = :survey AND (question.type = :radio OR question.type = :checkbox)
			GROUP BY question_id, option_id
		SQL;
		$rsm = (new ResultSetMapping())
			->addIndexByScalar('option_id')
			->addScalarResult('id', 'id')
			->addScalarResult('question_id', 'question_id')
			->addScalarResult('option_id', 'option_id')
			->addScalarResult('amount', 'amount')
			->addScalarResult('total', 'total');
		$query = $entityManager->createNativeQuery($sql, $rsm)
			->setParameter(':survey', $survey)
			->setParameter(':radio', 'radio')
			->setParameter(':checkbox', 'checkbox');

		return $query->getResult();
	}

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
