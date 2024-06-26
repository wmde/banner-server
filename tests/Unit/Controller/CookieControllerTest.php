<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Controller;

use DateInterval;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WMDE\BannerServer\Controller\AbstractCookieController;
use WMDE\BannerServer\Tests\Utils\TestCookieController;

#[CoversClass( AbstractCookieController::class )]
class CookieControllerTest extends TestCase {

	public function test_given_no_category_then_no_cookie_is_set(): void {
		$controller = new TestCookieController( 'P1D' );

		$response = $controller->index( new Request() );

		$this->assertCount( 0, $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT ) );
		$this->assertNotSame( Response::HTTP_OK, $response->getStatusCode() );
	}

	public function test_given_invalid_category_then_no_cookie_is_set(): void {
		$controller = new TestCookieController( 'P1D' );

		$response = $controller->index( new Request( [ 'c' => ':break-my-yaml!' ] ) );

		$this->assertCount( 0, $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT ) );
		$this->assertNotSame( Response::HTTP_OK, $response->getStatusCode() );
	}

	public function test_given_a_category_then_category_cookie_is_set(): void {
		$controller = new TestCookieController( 'P180D' );

		$response = $controller->index( new Request( [ 'c' => 'fundraising,fundraising_next' ] ) );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate = ( new DateTime() )->add( new DateInterval( 'P180D' ) );

		$this->assertSame( 'fundraising,fundraising_next', $firstCookie->getValue() );
		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
		$this->assertSame( 'text/html', $response->headers->get( 'content-type', '' ) );
	}

	public function test_given_image_content_type_zero_byte_image_is_returned(): void {
		$controller = new TestCookieController( 'P180D' );

		$request = new Request( [ 'c' => 'fundraising,fundraising_next' ] );
		$request->headers->set( 'accept', 'image/gif,image/png,image/*' );
		$response = $controller->index( $request );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$this->assertNotEmpty( $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT ) );
		$this->assertSame( 'image/png', $response->headers->get( 'content-type', '' ) );
		$this->assertSame( '', $response->getContent() );
	}

}
