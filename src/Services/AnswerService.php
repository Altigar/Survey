<?php

namespace App\Services;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Survey;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AnswerService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private QuestionRepository $questionRepository,
	) {}

	public function create(array $requestQuestionData, Survey $survey, Pass $pass): void
	{
		$questions = $this->questionRepository->findIndexedBySurveyWithIndexedOptions($survey);

		foreach ($requestQuestionData as $questionData) {
			if (!$question = $questions[$questionData->getId()] ?? null) {
				throw new BadRequestHttpException();
			}
			foreach ($questionData->getAnswers() as $answerData) {
				$answer = Answer::create($question, $pass);
				switch ($question->getType()) {
					case Question::TYPE_RADIO:
					case Question::TYPE_CHECKBOX:
						$options = $question->getOptions()->toArray();
						if (!$option = $options[$answerData->getOption()->getId()] ?? null) {
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
