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
		$responseCopy = clone $response;

		$exception = $event->getThrowable();
		if ($exception instanceof ValidationExceptionInterface) {
			$response->setData($exception->getErrors());
			$response->setStatusCode($exception->getStatusCode());
			foreach ($exception->getHeaders() as $headerKey => $headerValue) {
				$response->headers->set($headerKey, $headerValue);
			}
		} elseif ($exception instanceof NotNormalizableValueException) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
		}

		if ($response != $responseCopy) {
			$event->setResponse($response);
		}
	}
}