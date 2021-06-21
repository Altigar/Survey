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
		$query = $this->getEntityManager()->createNativeQuery($sql, $rsm)
			->setParameter(':survey', $survey)
			->setParameter(':text', 'text')
			->setParameter(':string', 'string');

		return $query->getResult();
	}

	public function findChoiceStatsBySurvey(int $survey): array
	{
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
		$query = $this->getEntityManager()->createNativeQuery($sql, $rsm)
			->setParameter(':survey', $survey)
			->setParameter(':radio', 'radio')
			->setParameter(':checkbox', 'checkbox');

		return $query->getResult();
	}

	public function findScaleStatsBySurvey(int $survey): array
	{
		$sql = <<<SQL
			SELECT question_id, option_id, count(option_id) AS amount, sum(count(option_id)) OVER(partition by question_id) as total, scale_value
			FROM answer
			JOIN question ON question.id = answer.question_id
			JOIN survey ON question.survey_id = survey.id
			WHERE survey_id = :survey AND question.type = :scale
			GROUP BY question_id, option_id, scale_value
		SQL;
		$rsm = (new ResultSetMapping())
			->addScalarResult('id', 'id')
			->addScalarResult('question_id', 'question_id')
			->addScalarResult('option_id', 'option_id')
			->addScalarResult('amount', 'amount')
			->addScalarResult('total', 'total')
			->addScalarResult('scale_value', 'scale_value');
		$query = $this->getEntityManager()->createNativeQuery($sql, $rsm)
			->setParameter(':survey', $survey)
			->setParameter(':scale', 'scale');

		return $query->getResult();
	}
}
