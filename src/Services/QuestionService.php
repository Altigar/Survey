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

	public function getBySurvey(int $survey): array
	{
		return $this->entityManager->getRepository(Question::class)->findBy(['survey' => $survey], ['ordering' => 'asc']);
	}

	public function create(int $id, array $data): bool
	{
		$repository = $this->entityManager->getRepository(Survey::class);
		/** @var $survey Survey */
		if ($survey = $repository->find($id)) {
			$type = $this->accessor->getValue($data, '[type]');
			$question = (new Question())
				->setSurvey($survey)
				->setType($this->accessor->getValue($data, '[type]'))
				->setCreatedAt(new \DateTime('now'))
				->setOrdering($this->accessor->getValue($data, '[ordering]'))
				->addOption(new Option());
			if ($type == 'text') {
				$question->setRow(3);
			}
			$this->entityManager->persist($question);
			$this->entityManager->flush();
		}
		return (bool)$survey;
	}

	public function updateChoice(array $data): bool
	{
		$repository = $this->entityManager->getRepository(Question::class);
		/** @var $question Question */
		if ($question = $repository->findById($this->accessor->getValue($data, '[id]'))) {
			$question->setType($this->accessor->getValue($data, '[type]'));
			$question->setText($this->accessor->getValue($data, '[text]'));
			$options = $question->getOptions()->toArray();
			$rawOptions = $data['options'] ?? [];

			if ($optionsIds = array_column($rawOptions, 'id', 'id')) {
				$removeOptions = array_diff_key($options, $optionsIds);
				foreach ($removeOptions as $remove) {
					$question->removeOption($remove);
				}
			}

			foreach ($rawOptions as $rawOption) {
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
		return (bool)$question;
	}

	public function updateNote(array $data): bool
	{
		$repository = $this->entityManager->getRepository(Question::class);
		/** @var $question Question */
		if ($question = $repository->findById($this->accessor->getValue($data, '[id]'))) {
			$type = $this->accessor->getValue($data, '[type]');
			$question->setType($type);
			$question->setText($this->accessor->getValue($data, '[text]'));
			if ($type == 'text') {
				$question->setRow($this->accessor->getValue($data, '[row]'));
			}
			$this->entityManager->persist($question);
			$this->entityManager->flush();
		}
		return (bool)$question;
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