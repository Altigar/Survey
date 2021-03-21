<?php

namespace App\Services;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;

class QuestionService
{
	public function __construct(private EntityManagerInterface $entityManager) {}

	public function create(Question $question, int $surveyId): void
	{
		$repository = $this->entityManager->getRepository(Survey::class);
		/** @var $survey Survey */
		$survey = $repository->find($surveyId);
		$question->setSurvey($survey);
		$question->setCreatedAt(new \DateTime('now'));
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function update(array $data): void
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$question = $repository->findById($data['id'] ?? null);
		$question->setType($data['type'] ?? null);
		$question->setText($data['text'] ?? null);
		$options = $question->getOptions()->toArray();
		foreach ($data['options'] ?? [] as $rawOption) {
			$optionId = $rawOption['id'] ?? null;
			if ($option = $options[$optionId] ?? null) {
				$option->setText($rawOption['text'] ?? null)
					->setOrdering($rawOption['ordering'] ?? null);
			} else {
				$option = (new Option())
					->setText($rawOption['text'] ?? null)
					->setQuestion($question)
					->setOrdering($rawOption['ordering'] ?? null);
				$question->addOption($option);
			}
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}
}