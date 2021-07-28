<?php

namespace App\Services;

use App\Data\Content\Create\QuestionData;
use App\Data\Content\Update\QuestionData as QuestionDataUpdate;
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

	public function create(Survey $survey, QuestionData $questionData): int
	{
		$question = Question::createContent($survey, $questionData->getType(), $questionData->getOrdering());
		$data = match ($question->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => ['text' => 'First option'],
			Question::TYPE_TEXT => ['row' => Option::DEFAULT_ROW],
			Question::TYPE_SCALE => ['scale' => Option::DEFAULT_SCALE],
			default => [],
		};
		$option = Option::createContent($data);
		$question->addOption($option);
		$this->entityManager->persist($question);
		$this->entityManager->flush();

		return $question->getId();
	}

	public function updateChoice(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->updateContent($questionData->getIsRequired(), $questionData->getText());
		//remove
		$optionIds = ObjectUtil::getColumn($questionData->getOptions(), 'id');
		foreach ($question->getOptions()->toArray() as $option) {
			if (!in_array($option->getId(), $optionIds)) {
				$question->removeOption($option);
			}
		}
		//add
		$options = ObjectUtil::reindex($question->getOptions()->toArray(), 'id');
		foreach ($questionData->getOptions() as $optionData) {
			if (!$optionData->getId()) {
				$option = Option::createContent(['text' => $optionData->getText()]);
				$question->addOption($option);
			} elseif ($option = $this->accessor->getValue($options, '[' . $optionData->getId() . ']')) {
				$option->updateContent(text: $optionData->getText());
			}
		}
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateString(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->updateContent($questionData->getIsRequired(), $questionData->getText());

		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateText(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->updateContent($questionData->getIsRequired(), $questionData->getText());

		$optionData = ArrayUtil::first($questionData->getOptions());
		$option = ArrayUtil::first($question->getOptions()->toArray());
		$option->updateContent(row: $optionData->getRow());

		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	public function updateScale(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->updateContent($questionData->getIsRequired(), $questionData->getText());

		$optionData = ArrayUtil::first($questionData->getOptions());
		$option = $question->getOptions()->first();
		$option->updateContentScale($optionData->getScale(), $optionData->getScaleFromText(), $optionData->getScaleToText());

		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}
}