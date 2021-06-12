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

	public function create(Survey $survey, Question $question): void
	{
		$question->setSurvey($survey)
			->setCreatedAt(new \DateTime('now'));
		$option = new Option();
		if ($question->getType() == 'text') {
			$option->setRow(3);
		} elseif ($question->getType() == 'scale') {
			$option->setScale(10);
		}
		$question->addOption($option);
		$this->entityManager->persist($question);
		$this->entityManager->flush();
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
			if ($question->getType() == 'text') {
				$option = ArrayUtil::first($question->getOptions()->toArray());
				$optionDb = ArrayUtil::first($questionDb->getOptions()->toArray());
				$optionDb->setRow($option->getRow());
			}
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
}