<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WMDE\BannerServer\Controller\DonationFinishedController;
use PHPUnit\Framework\TestCase;

class DonationFinishedControllerTest extends TestCase {

	public function test_given_no_category_then_no_cookie_is_set(): void {
		$controller = new DonationFinishedController( 'P1D' );

		$response = $controller->index( new Request() );

		$this->assertEmpty( $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT ) );
	}

	public function test_given_invalid_category_then_no_cookie_is_set(): void {
		$controller = new DonationFinishedController( 'P1D' );

		$response = $controller->index( new Request( ['c' => ':break-my-yaml!'] ) );

		$this->assertEmpty( $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT ) );
	}

	public function test_given_a_category_then_category_cookie_is_set(): void {
		$controller = new DonationFinishedController( 'P180D' );

		$response = $controller->index( new Request( ['c' => 'fundraising,fundraising_next'] ) );

		$firstCookie = $response->headers->getCookies( ResponseHeaderBag::COOKIES_FLAT )[0];
		$expectedDate =( new \DateTime() )->add( new \DateInterval( 'P180D' ) );

		$this->assertSame( 'fundraising,fundraising_next', $firstCookie->getValue() );
		$this->assertEqualsWithDelta( $expectedDate->getTimestamp(), $firstCookie->getExpiresTime(), 5 );
	}

}
