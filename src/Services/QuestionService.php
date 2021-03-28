<?php

namespace App\Services;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class QuestionService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private PropertyAccessorInterface $accessor,
	) {}

	public function create(int $id, array $data): bool
	{
		$repository = $this->entityManager->getRepository(Survey::class);
		/** @var $survey Survey */
		if ($survey = $repository->find($id)) {
			$option = (new Option())->setOrdering(1);
			$question = (new Question())
				->setSurvey($survey)
				->setType($this->accessor->getValue($data, '[type]'))
				->setCreatedAt(new \DateTime('now'))
				->addOption($option);
			$this->entityManager->persist($question);
			$this->entityManager->flush();
		}
		return (bool)$survey;
	}

	public function update(array $data): void
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$question = $repository->findById($this->accessor->getValue($data, '[id]'));
		$question->setType($this->accessor->getValue($data, '[type]'));
		$question->setText($this->accessor->getValue($data, '[text]'));
		$options = $question->getOptions()->toArray();
		foreach ($data['options'] ?? [] as $rawOption) {
			$optionId = $this->accessor->getValue($rawOption, '[id]');
			if ($optionId && $option = $this->accessor->getValue($options, '[' . $optionId . ']')) {
				$option->setText($this->accessor->getValue($rawOption, '[text]'))
					->setOrdering($this->accessor->getValue($rawOption, '[ordering]'));
			} else {
				$option = (new Option())
					->setText($this->accessor->getValue($rawOption, '[text]'))
					->setQuestion($question)
					->setOrdering($this->accessor->getValue($rawOption, '[ordering]'));
				$question->addOption($option);
			}
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function delete(array $data): bool
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$id = $this->accessor->getValue($data, '[id]');
		if ($question = $repository->find((int)$id)) {
			$this->entityManager->remove($question);
			$this->entityManager->flush();
		}
		return (bool)$question;
	}
}