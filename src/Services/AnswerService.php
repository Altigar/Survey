<?php

namespace App\Services;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Person;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ObjectUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AnswerService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
	) {}

	public function create(array $requestQuestionData, Survey $survey, Person $person): void
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$questions = ObjectUtil::reindex($repository->findBy(['survey' => $survey]), 'id');
		$pass = Pass::create($survey, $person);

		foreach ($requestQuestionData as $questionData) {
			if (!$question = $questions[$questionData->getId()]) {
				throw new BadRequestHttpException();
			}
			foreach ($questionData->getAnswers() as $answerData) {
				$answer = Answer::create($person, $question, $pass);
				switch ($question->getType()) {
					case Question::TYPE_RADIO:
					case Question::TYPE_CHECKBOX:
						$options = ObjectUtil::reindex($question->getOptions()->toArray(), 'id');
						if (!$option = $options[$answerData->getOption()->getId()]) {
							throw new BadRequestHttpException();
						}
						$answer->choiceType($option);
						break;
					case Question::TYPE_STRING:
					case Question::TYPE_TEXT:
						$answer->textType($answerData->getText());
						break;
					case Question::TYPE_SCALE:
						$answer->scaleType($answerData->getScaleValue());
						break;
					default:
						throw new BadRequestHttpException();
				}
				$this->entityManager->persist($answer);
			}
		}
		$this->entityManager->flush();
	}
}
