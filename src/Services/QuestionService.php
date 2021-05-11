<?php

namespace App\Services;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ArrayUtil;
use App\Utils\ObjectUtil;
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
				->setOrdering($this->accessor->getValue($data, '[ordering]'));
			$option = new Option();
			if ($type == 'text') {
				$question->setRow(3);
			} elseif ($type == 'scale') {
				$option->setScale(10);
			}
			$question->addOption($option);
			$this->entityManager->persist($question);
			$this->entityManager->flush();
		}
		return (bool)$survey;
	}

	public function updateChoice(Question $question): ?Question
	{
		$repository = $this->entityManager->getRepository(Question::class);
		if ($questionDb = $repository->find($question->getId())) {
			$optionIds = ObjectUtil::getColumn($question->getOptions()->toArray(), 'id');
			foreach ($questionDb->getOptions()->toArray() as $option) {
				if (!in_array($option->getId(), $optionIds)) {
					$questionDb->removeOption($option);
				}
			}
			$options = ObjectUtil::reindex($questionDb->getOptions()->toArray(), 'id');
			foreach ($question->getOptions()->toArray() as $option) {
				if (!$option->getId()) {
					$questionDb->addOption($option);
				} elseif ($obj = $this->accessor->getValue($options, '[' . $option->getId() . ']')) {
					$obj->setText($option->getText());
				}
			}
			$questionDb->setText($question->getText());
			$this->entityManager->persist($questionDb);
			$this->entityManager->flush();
		}
		return $questionDb;
	}

	public function updateNote(Question $question): ?Question
	{
		$repository = $this->entityManager->getRepository(Question::class);
		if ($questionDb = $repository->find($question->getId())) {
			$questionDb->setText($question->getText());
			$this->entityManager->persist($questionDb);
			$this->entityManager->flush();
		}
		return $questionDb;
	}

	public function updateScale(Question $question): ?Question
	{
		$repository = $this->entityManager->getRepository(Question::class);
		if ($questionDb = $repository->find($question->getId())) {
			$questionDb->setText($question->getText());
			if ($optionDb = $questionDb->getOptions()->first()) {
				$option = ArrayUtil::first($question->getOptions()->toArray());
				$optionDb->setScale($option->getScale())
					->setScaleFromText($option->getScaleFromText())
					->setScaleToText($option->getScaleToText());
			}
			$this->entityManager->persist($questionDb);
			$this->entityManager->flush();
		}
		return $questionDb;
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