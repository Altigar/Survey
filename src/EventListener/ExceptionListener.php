<?php

namespace App\EventListener;

use App\Exception\ValidationExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ExceptionListener
{
	public function onKernelException(ExceptionEvent $event): void
	{
		$response = $event->getRequest()->isXmlHttpRequest() ? new JsonResponse() : new Response();

		$exception = $event->getThrowable();
		if ($exception instanceof ValidationExceptionInterface) {
			$response->setData($exception->getErrors());
			$response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
		} elseif ($exception instanceof NotNormalizableValueException) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
		}

		$event->setResponse($response);
	}
}