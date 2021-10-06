<?php

namespace App\Security;

use App\Entity\Question;
use App\Entity\Survey;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContentVoter extends Voter
{
	const VIEW = 'view';
	const CREATE = 'create';
	const UPDATE = 'update';
	const DELETE = 'delete';

	protected function supports(string $attribute, $subject): bool
	{
		return in_array($attribute, [self::VIEW, self::CREATE, self::UPDATE, self::DELETE]);
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($subject instanceof Survey && in_array($attribute, [self::VIEW, self::CREATE])) {
			return $token->getUser() == $subject->getPerson();
		} elseif ($subject instanceof Question && in_array($attribute, [self::UPDATE, self::DELETE])) {
			return $token->getUser() == $subject->getSurvey()->getPerson();
		} else {
			return false;
		}
	}
}