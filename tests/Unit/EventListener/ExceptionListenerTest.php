<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\EventListener;

use Exception;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WMDE\BannerServer\EventListener\ExceptionListener;

#[CoversClass( ExceptionListener::class )]
class ExceptionListenerTest extends KernelTestCase {

	private EventDispatcher $dispatcher;

	public function setUp(): void {
		$this->dispatcher = new EventDispatcher();
	}

	/** solves tests marked as "risky" by phpunit, error was: "Test code or tested code did not remove its own exception handlers" */
	public function tearDown(): void {
		restore_exception_handler();
	}

	public function testGivenExceptionForJavaScriptUrl_thenEmptyInternalServerErrorMessageIsReturned(): void {
		$this->withExceptionListener( new TestHandler() );

		$event = $this->makeDispatchedExceptionEvent( 'some/url/which/implies/javascript.js' );

		$response = $event->getResponse();

		$this->assertNotNull( $response );
		$this->assertEquals( 'application/javascript; charset=UTF-8', $response->headers->get( 'Content-Type' ) );
		$this->assertEquals( Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode() );
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
			->willReturn( $requestUrl );
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
			new Exception( 'This is the error message' )
		);
		$this->dispatcher->dispatch( $event, 'onKernelException' );
		return $event;
	}

}
