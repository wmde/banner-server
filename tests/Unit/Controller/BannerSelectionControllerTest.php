<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\Controller\BannerSelectionController;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\Tests\Fixtures\CampaignFixture;
use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\Tests\Utils\FakeRandomIntegerGenerator;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;

/**
 * @covers \WMDE\BannerServer\Controller\BannerSelectionController
 */
class BannerSelectionControllerTest extends TestCase {

	private const BANNER_PATH = '/test_banners/';

	public function test_given_controller_receives_cookies_then_it_passes_cookie_values_to_use_case(): void {
		$mockUseCase = $this->createMock( BannerSelectionUseCase::class );
		$mockUseCase->expects( $this->once() )->method( 'selectBanner' )->with(
			VisitorFixture::getTestVisitor()
		);
		$controller = new BannerSelectionController( $mockUseCase, self::BANNER_PATH );
		$controller->selectBanner( VisitorFixture::getReturningVisitorRequest() );
	}

	public function test_given_no_cookies_then_it_assigns_default_values(): void {
		$mockUseCase = $this->createMock( BannerSelectionUseCase::class );
		$mockUseCase->expects( $this->once() )->method( 'selectBanner' )->with(
			VisitorFixture::getFirstTimeVisitor()
		);
		$controller = new BannerSelectionController( $mockUseCase, self::BANNER_PATH );
		$controller->selectBanner( new Request() );
	}

	public function test_given_no_cookies_passed_through_request_then_it_creates_cookies_with_updated_default_values(): void {
		$testUseCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( 10 ),
			new FakeRandomIntegerGenerator( 100 )
		);
		$controller = new BannerSelectionController( $testUseCase, self::BANNER_PATH );
		$response = $controller->selectBanner( new Request() );

		$cookies = $response->headers->getCookies();

		$this->assertCount( 2, $cookies );

		$this->assertEquals( BannerSelectionController::BUCKET_COOKIE, $cookies[0]->getName() );
		$this->assertEquals( 'test', $cookies[0]->getValue() );
		$this->assertEquals(
			( new \DateTime( '2099-12-31 23:59:59' ) )->modify( '+2 week' )->getTimestamp(),
			$cookies[0]->getExpiresTime(),
			'Cookie life-time should be the campaign expiration date plus two weeks.'
		);

		$this->assertEquals( BannerSelectionController::IMPRESSION_COUNT_COOKIE, $cookies[1]->getName() );
		$this->assertSame( '1', $cookies[1]->getValue() );
		$this->assertEquals(
			( new \DateTime( 'midnight first day of january next year' ) )->getTimestamp(),
			$cookies[1]->getExpiresTime(),
			'Impression cookie should expire after the campaign season has ended.'
		);
	}

	public function test_given_banner_is_to_be_shown_then_it_returns_redirect_response(): void {
		$testUseCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( 10 ),
			new FakeRandomIntegerGenerator( 100 )
		);
		$controller = new BannerSelectionController( $testUseCase, self::BANNER_PATH );
		$response = $controller->selectBanner( new Request() );

		$this->assertInstanceOf( RedirectResponse::class, $response );
		$this->assertEquals( '/test_banners/TestBanner.js', $response->headers->get( 'location' ) );
	}

	public function test_given_impression_limit_is_reached_then_it_returns_http_ok_response(): void {
		$testUseCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( VisitorFixture::VISITOR_TEST_IMPRESSION_COUNT ),
			new FakeRandomIntegerGenerator( 100 )
		);
		$controller = new BannerSelectionController( $testUseCase, self::BANNER_PATH );
		$response = $controller->selectBanner( VisitorFixture::getReturningVisitorRequest() );

		$this->assertEquals( Response::HTTP_OK, $response->getStatusCode() );
		$this->assertEquals( '// Sorry, no banner for you!', $response->getContent() );
	}
}
