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

	public function create(array $requestData, Survey $survey, Person|UserInterface $person): void
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$questions = ObjectUtil::reindex($repository->findBy(['survey' => $survey]), 'id');

		foreach ($requestData as $questionData) {
			$question = $questions[$questionData->getId()];
			foreach ($questionData->getAnswers() as $answerData) {
				$options = ObjectUtil::reindex($question->getOptions()->toArray(), 'id');
				$data = [
					'person' => $person,
					'question' => $question,
					'pass' => Pass::create($survey, $person),
					'option' => $options[$answerData->getOption()->getId()]
				];
				$type = $question->getType();
				if (in_array($type, [Question::TYPE_STRING, Question::TYPE_TEXT])) {
					$data['text'] = $answerData->getText();
				} elseif ($type == Question::TYPE_SCALE) {
					$data['scale_value'] = $answerData->getScaleValue();
				}
				$answer = Answer::create($data);
				$this->entityManager->persist($answer);
			}
		}
		$this->entityManager->flush();
	}
}
