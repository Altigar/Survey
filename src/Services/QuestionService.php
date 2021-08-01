<?php

namespace App\Services;

use App\Data\Content\Create\QuestionData as QuestionDataCreate;
use App\Data\Content\Update\QuestionData as QuestionDataUpdate;
use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ArrayUtil;
use App\Utils\ObjectUtil;
use Doctrine\ORM\EntityManagerInterface;

class QuestionService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
	) {}

	public function create(Survey $survey, QuestionDataCreate $questionData): int
	{
		$question = Question::createContent($survey, $questionData->getType(), $questionData->getOrdering());
		$option = match ($question->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => Option::createContent(text: 'First option'),
			Question::TYPE_TEXT => Option::createContent(row: Option::DEFAULT_ROW),
			Question::TYPE_SCALE => Option::createContent(scale: Option::DEFAULT_SCALE),
			default => Option::createContent(),
		};
		$question->addOption($option);
		$this->entityManager->persist($question);
		$this->entityManager->flush();

		return $question->getId();
	}

	public function update(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->updateContent($questionData->getIsRequired(), $questionData->getText());
		$question = match ($question->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => $this->choice($question, $questionData),
			Question::TYPE_TEXT => $this->text($question, $questionData),
			Question::TYPE_SCALE => $this->scale($question, $questionData),
			default => $question,
		};
		$this->entityManager->persist($question);
		$this->entityManager->flush();
	}

	private function choice(Question $question, QuestionDataUpdate $questionData): Question
	{
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
				$option = Option::createContent(text: $optionData->getText(), ordering: $optionData->getOrdering());
				$question->addOption($option);
			} elseif ($option = $options[$optionData->getId()] ?? null) {
				$option->updateContent(text: $optionData->getText());
			}
		}

		return $question;
	}

	private function text(Question $question, QuestionDataUpdate $questionData): Question
	{
		$optionData = ArrayUtil::first($questionData->getOptions());
		$option = ArrayUtil::first($question->getOptions()->toArray());
		$option->updateContent(row: $optionData->getRow());

		return $question;
	}

	private function scale(Question $question, QuestionDataUpdate $questionData): Question
	{
		$optionData = ArrayUtil::first($questionData->getOptions());
		$option = $question->getOptions()->first();
		$option->updateContentScale($optionData->getScale(), $optionData->getScaleFromText(), $optionData->getScaleToText());

		return $question;
	}
}