<?php

namespace App\Services;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Person;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ObjectUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AnswerService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
	) {}

	public function create(array $data, Survey $survey, Person|UserInterface $person): void
	{
		$pass = (new Pass())
			->setPerson($person)
			->setSurvey($survey)
			->setCreatedAt(new \DateTime('now'));

		$repository = $this->entityManager->getRepository(Question::class);
		$questions = ObjectUtil::reindex($repository->findBy(['survey' => $survey]), 'id');

		foreach ($data as $questionData) {
			$question = $questions[$questionData->getId()];
			foreach ($questionData->getAnswers() as $answerData) {
				$options = ObjectUtil::reindex($question->getOptions()->toArray(), 'id');
				$answer = (new Answer())
					->setPerson($person)
					->setQuestion($question)
					->setPass($pass)
					->setOption($options[$answerData->getOption()->getId()]);
				if (in_array($question->getType(), [Question::TYPE_STRING, Question::TYPE_TEXT])) {
					$answer->setText($answerData->getText());
				}
				$this->entityManager->persist($answer);
			}
		}
		$this->entityManager->flush();
	}
}
