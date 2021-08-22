<?php

namespace App\Twig;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExtensionSerializer extends AbstractExtension
{
	public function __construct(
		private SerializerInterface $serializer,
	) {}

	public function getFilters(): array
	{
		return [
			new TwigFilter('serialize', [$this, 'serialize']),
		];
	}

	public function serialize(array|object $data, string $format, array $ignore = []): string
	{
		return $this->serializer->serialize($data, $format, [AbstractNormalizer::IGNORED_ATTRIBUTES => $ignore]);
	}
}