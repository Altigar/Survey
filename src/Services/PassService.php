<?php

namespace App\Services;

use App\Entity\ExternalPerson;
use App\Entity\Pass;
use App\Entity\Person;
use App\Entity\Survey;
use App\Repository\ExternalPersonRepository;

class PassService
{
	public function __construct(
		private ExternalPersonRepository $externalPersonRepository
	) {}

	public function create(Survey $survey, ?Person $person, string $ip): Pass
	{
		$pass = Pass::create($survey);
		if ($person) {
			$pass->setPerson($person);
		} else {
			if ($externalPerson = $this->externalPersonRepository->findOneBy(['ip' => $ip])) {
				$pass->setExternalPerson($externalPerson);
			} else {
				$pass->setExternalPerson(ExternalPerson::create($ip));
			}
		}

		return $pass;
	}
}
