<?php

namespace App\EventListener;

use App\Controller\CsrfTokenControllerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ControllerListener
{
	public function __construct(
		private CsrfTokenManagerInterface $csrfTokenManager
	) {}

	public function onKernelController(ControllerEvent $event)
	{
		$controller = $event->getController();
		// when a controller class defines multiple action methods, the controller
		// is returned as [$controllerInstance, 'methodName']
		if (is_array($controller)) {
			$controller = $controller[0];
		}

		$request = $event->getRequest();
		if ($controller instanceof CsrfTokenControllerInterface && in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
			if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('default', $request->headers->get('X-CSRF-TOKEN')))) {
				throw new AccessDeniedHttpException('Invalid CSRF token. Try reloading this page');
			}
		}
	}
}