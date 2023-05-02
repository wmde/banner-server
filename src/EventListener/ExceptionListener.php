<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * @license GPL-2.0-or-later
 */
class ExceptionListener {

	public function __construct(
		private readonly LoggerInterface $logger
	) {
	}

	public function onKernelException( ExceptionEvent $event ): void {
		$this->logger->critical(
			$event->getThrowable()->getMessage(),
			[ 'exception' => $event->getThrowable() ]
		);

		if ( preg_match( '/^.*\.js$/', $event->getRequest()->getPathInfo() ) ) {
			$response = new Response();
			$response->headers->set( 'Content-Type', 'application/javascript; charset=UTF-8' );
			$response->setStatusCode( Response::HTTP_INTERNAL_SERVER_ERROR );
			$event->setResponse( $response );
		}
	}
}
