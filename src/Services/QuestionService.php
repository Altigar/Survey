<?php

namespace App\Services;

use App\Data\Content\QuestionDataCreate;
use App\Data\Content\QuestionDataUpdate;
use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ObjectUtil;

class QuestionService
{
	public function create(Survey $survey, QuestionDataCreate $questionData): Question
	{
		$question = Question::create($survey, $questionData->getType(), $questionData->getOrdering());
		match ($question->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => $question->addOption(Option::create()),
			Question::TYPE_TEXT => $question->textType(Question::DEFAULT_ROW),
			Question::TYPE_SCALE => $question->scaleType(Question::DEFAULT_SCALE),
			default => null,
		};
		return $question;
	}

	public function update(Question $question, QuestionDataUpdate $questionData): void
	{
		$question = $question->update($questionData->getIsRequired(), $questionData->getText());
		match ($question->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => $this->choice($question, $questionData),
			Question::TYPE_TEXT => $question->textType($questionData->getRow()),
			Question::TYPE_SCALE => $question->scaleType(
				$questionData->getScale(),
				$questionData->getScaleFromText(),
				$questionData->getScaleToText()
			),
			default => $question,
		};
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
				$option = Option::create($optionData->getText(), $optionData->getOrdering());
				$question->addOption($option);
			} elseif ($option = $options[$optionData->getId()] ?? null) {
				$option->update($optionData->getText());
			}
		}

		return $question;
	}
}