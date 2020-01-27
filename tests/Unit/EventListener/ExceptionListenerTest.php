<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\EventListener;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use WMDE\BannerServer\EventListener\ExceptionListener;

/**
 * @covers \WMDE\BannerServer\EventListener\ExceptionListener
 */
class ExceptionListenerTest extends \PHPUnit\Framework\TestCase {

	public function testGivenExceptionForJavaScriptUrl_thenEmptyInternalServerErrorMessageIsReturned(): void {
		$testHandler = new TestHandler();

		$event = $this->newGetResponseForExceptionEventMock(
			$this->newRequestMock( 'some/url/which/implies/javascript.js' )
		);

		$event->expects( $this->once() )
			->method( 'setResponse' )
			->with(
				$this->callback(
					function ( $response ) {
						return $response->getContent() === '' &&
							$response->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR;
					}
				)
			);

		$listener = new ExceptionListener( new Logger( 'TestLogger', [ $testHandler ] ) );
		$listener->onKernelException( $event );
	}

	public function testGivenExceptionForNonJavaScriptUrl_thenEmptyNotFoundErrorMessageIsReturned(): void {
		$testHandler = new TestHandler();

		$event = $this->newGetResponseForExceptionEventMock(
			$this->newRequestMock( 'some/url/which/implies/not/javascript.html' )
		);

		$event->expects( $this->never() )->method( 'setResponse' );

		$listener = new ExceptionListener( new Logger( 'TestLogger', [ $testHandler ] ) );
		$listener->onKernelException( $event );
	}

	public function testGivenExceptionForJavaScriptUrl_thenErrorsAreLogged(): void {
		$testHandler = new TestHandler();
		$response = $this->newGetResponseForExceptionEventMock(
			$this->newRequestMock( 'some/url/which/implies/javascript.js' )
		);

		$listener = new ExceptionListener( new Logger( 'TestLogger', [ $testHandler ] ) );
		$listener->onKernelException( $response );

		$this->assertTrue( $testHandler->hasCriticalRecords() );
	}

	/**
	 * @return ExceptionEvent|MockObject
	 */
	private function newGetResponseForExceptionEventMock( Request $request ): ExceptionEvent {
		$response = $this->createMock( ExceptionEvent::class );

		$response->expects( $this->once() )
			->method( 'getRequest' )
			->will( $this->returnValue( $request ) );

		$response->expects( $this->exactly( 2 ) )
			->method( 'getThrowable' )
			->will( $this->returnValue( new \Exception( '❌❌❌ Fatal Error: Not enough emojis used. ❌❌❌' ) ) );

		return $response;
	}

	private function newRequestMock( string $requestUrl ): Request {
		$request = $this->createMock( Request::class );
		$request->expects( $this->once() )
			->method( 'getPathInfo' )
			->will( $this->returnValue( $requestUrl ) );
		return $request;
	}

}
