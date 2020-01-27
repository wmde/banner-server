<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * @license GNU GPL v2+
 */
class ExceptionListener {

	private $logger;

	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
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