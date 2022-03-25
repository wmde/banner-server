<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\EventListener;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WMDE\BannerServer\EventListener\ExceptionListener;

/**
 * @covers \WMDE\BannerServer\EventListener\ExceptionListener
 */
class ExceptionListenerTest extends KernelTestCase {

	private EventDispatcher $dispatcher;

	public function setUp(): void {
		$this->dispatcher = new EventDispatcher();
	}

	public function testGivenExceptionForJavaScriptUrl_thenEmptyInternalServerErrorMessageIsReturned(): void {
		$this->withExceptionListener( new TestHandler() );

		$event = $this->makeDispatchedExceptionEvent( 'some/url/which/implies/javascript.js' );

		$this->assertEquals( 'application/javascript; charset=UTF-8', $event->getResponse()->headers->get( 'Content-Type' ) );
		$this->assertEquals( Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode() );
	}

	public function testGivenExceptionForNonJavaScriptUrl_thenEmptyNotFoundErrorMessageIsReturned(): void {
		$this->withExceptionListener( new TestHandler() );

		$event = $this->makeDispatchedExceptionEvent( 'some/url/which/implies/not/javascript.html' );

		$this->assertNull( $event->getResponse() );
	}

	public function testGivenExceptionForJavaScriptUrl_thenErrorsAreLogged(): void {
		$testHandler = new TestHandler();
		$this->withExceptionListener( $testHandler );

		$event = $this->makeDispatchedExceptionEvent( 'some/url/which/implies/javascript.js' );

		$this->assertTrue( $testHandler->hasCriticalRecords() );
	}

	private function newRequestMock( string $requestUrl ): Request {
		$request = $this->createMock( Request::class );
		$request->expects( $this->once() )
			->method( 'getPathInfo' )
			->will( $this->returnValue( $requestUrl ) );
		return $request;
	}

	private function withExceptionListener( TestHandler $testHandler ): void {
		$listener = new ExceptionListener( new Logger( 'TestLogger', [ $testHandler ] ) );
		$this->dispatcher->addListener( 'onKernelException', [ $listener, 'onKernelException' ] );
	}

	private function makeDispatchedExceptionEvent( string $url ): ExceptionEvent {
		$event = new ExceptionEvent(
			self::bootKernel(),
			$this->newRequestMock( $url ),
			HttpKernelInterface::MAIN_REQUEST,
			new \Exception( 'This is the error message' )
		);
		$this->dispatcher->dispatch( $event, 'onKernelException' );
		return $event;
	}

}
