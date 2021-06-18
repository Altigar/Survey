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
		if (($type = $question->getType()) == Question::TYPE_TEXT) {
			$option->setRow(3);
		} elseif ($type == Question::TYPE_SCALE) {
			$option->setScale(10);
		}
		$question->addOption($option);
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateChoice(Question $question, Question $questionData): void
	{
		$question->setIsRequired($questionData->getIsRequired());
		$question->setText($questionData->getText());
		//remove
		$optionIds = ObjectUtil::getColumn($questionData->getOptions()->toArray(), 'id');
		foreach ($question->getOptions()->toArray() as $option) {
			if (!in_array($option->getId(), $optionIds)) {
				$question->removeOption($option);
			}
		}
		//add
		$options = ObjectUtil::reindex($question->getOptions()->toArray(), 'id');
		foreach ($questionData->getOptions()->toArray() as $option) {
			if (!$option->getId()) {
				$question->addOption($option);
			} elseif ($obj = $this->accessor->getValue($options, '[' . $option->getId() . ']')) {
				$obj->setText($option->getText());
			}
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateNote(Question $question, Question $questionData): void
	{
		$question->setIsRequired($questionData->getIsRequired());
		$question->setText($questionData->getText());
		if ($questionData->getType() == Question::TYPE_TEXT) {
			$option = ArrayUtil::first($questionData->getOptions()->toArray());
			$optionDb = ArrayUtil::first($question->getOptions()->toArray());
			$optionDb->setRow($option->getRow());
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateScale(Question $question, Question $questionData): void
	{
		$question->setIsRequired($questionData->getIsRequired());
		$question->setText($questionData->getText());
		if ($optionDb = $question->getOptions()->first()) {
			$option = ArrayUtil::first($questionData->getOptions()->toArray());
			$optionDb->setScale($option->getScale())
				->setScaleFromText($option->getScaleFromText())
				->setScaleToText($option->getScaleToText());
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}
}