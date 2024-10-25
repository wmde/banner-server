<?php

namespace WMDE\BannerServer\Tests\Unit\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WMDE\BannerServer\Controller\AlreadyDonatedController;

#[CoversClass( AlreadyDonatedController::class )]
class AlreadyDonatedControllerTest extends TestCase {

	public function test_given_no_parameter_controller_defaults_to_maximum_lifetime(): void {
		$controller = new AlreadyDonatedController();

		$response = $controller->index( new Request( [ 'c' => 'fundraising' ] ) );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate = ( new \DateTime() )->add( new \DateInterval( AlreadyDonatedController::MAX_INTERVAL_LENGTH ) );

		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
	}

	public function test_given_a_parameter_controller_defaults_to_parameter_lifetime_in_hours(): void {
		$controller = new AlreadyDonatedController();

		$response = $controller->index( new Request( [ 'c' => 'fundraising', 'd' => '48' ] ) );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate = ( new \DateTime() )->add( new \DateInterval( 'PT48H' ) );

		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
	}

	public function test_given_a_parameter_that_exceeds_maximum_duration_controller_defaults_to_maximum_lifetime(): void {
		$controller = new AlreadyDonatedController();

		$response = $controller->index( new Request( [ 'c' => 'fundraising', 'd' => 6 * 7 * 24 ] ) );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate = ( new \DateTime() )->add( new \DateInterval( AlreadyDonatedController::MAX_INTERVAL_LENGTH ) );

		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
	}

	public function test_given_a_parameter_with_invalid_value_controller_defaults_to_maximum_lifetime(): void {
		$controller = new AlreadyDonatedController();

		$response = $controller->index( new Request( [ 'c' => 'fundraising', 'd' => 'forever' ] ) );

		$this->assertSame( Response::HTTP_OK, $response->getStatusCode() );
		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate = ( new \DateTime() )->add( new \DateInterval( AlreadyDonatedController::MAX_INTERVAL_LENGTH ) );

		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
	}

}
