<?php

use PHPUnit\Framework\TestCase;
use App\Services\QuestionService;
use App\Data\Content\QuestionDataCreate;
use App\Entity\Survey;
use App\Entity\Question;

final class QuestionServiceTest extends TestCase
{
	private QuestionService $questionService;

	public function setUp(): void
	{
		$this->questionService = new QuestionService();
	}

	public function testCreateInstanceOfQuestion(): void
	{
		$questionData = new QuestionDataCreate('radio', 1, 1);
		$survey = new Survey();
		$this->assertInstanceOf(Question::class, $this->questionService->create($survey, $questionData));
	}

	/**
	 * @dataProvider getCreateTypesData
	 */
	public function testCreate(string $type, array $attrs = []): void
	{
		$questionData = new QuestionDataCreate($type, 1, 1);
		$survey = new Survey();
		$question = $this->questionService->create($survey, $questionData);
		foreach (array_merge(['text', 'created_at', 'survey', 'type'], $attrs) as $attr) {
			$this->assertObjectHasAttribute($attr, $question);
		}
		$this->assertEquals($type, $question->getType());
	}

	public function getCreateTypesData(): Generator
	{
		yield [Question::TYPE_RADIO, ['options']];
		yield [Question::TYPE_CHECKBOX, ['options']];
		yield [Question::TYPE_STRING];
		yield [Question::TYPE_TEXT, ['row']];
		yield [Question::TYPE_SCALE, ['scale', 'scale_from_text', 'scale_to_text']];
	}
}