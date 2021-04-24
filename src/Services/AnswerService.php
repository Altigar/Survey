<?php

namespace App\Services;

use App\Entity\Answer;
use App\Entity\Person;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AnswerService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private PropertyAccessorInterface $accessor,
	) {}

	public function create(array $data, int $id, Person|UserInterface $person): bool
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$questions = $repository->findBy(['survey' => $id]);
		foreach ($data as $questionId => $value) {
			if ($value && ($question = $this->getByValue($questions, $questionId))) {
				$answer = (new Answer())->setPerson($person)->setQuestion($question);
				$options = $question->getOptions()->toArray();
				if (in_array($question->getType(), ['string', 'text'])) {
					$answer->setText($value);
					$answer->setOption($options[0]);
				} elseif ($question->getType() == 'radio') {
					$option = $this->getByValue($options, $value);
					$answer->setOption($option);
				} elseif ($question->getType() == 'checkbox') {
					foreach ($value as $optionId) {
						$answer = (new Answer())->setPerson($person)->setQuestion($question);
						if ($option = $this->getByValue($options, $optionId)) {
							$answer->setOption($option);
						}
						$this->entityManager->persist($answer);
					}
					continue;
				}
				$this->entityManager->persist($answer);
			}
		}
		$this->entityManager->flush();
		return (bool)$data;
	}
	
	public function getByValue($data, $value)
	{
		foreach ($data as $obj) {
			if ($obj->getId() == $value) {
				return $obj;
			}
		}
		return null;
	}
}
