<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Controller;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\Controller\BannerSelectionController;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;
use WMDE\BannerServer\UseCase\BannerSelection\Visitor;
use WMDE\BannerServer\Utils\RandomInteger;

class BannerSelectionControllerTest extends \PHPUnit\Framework\TestCase {

	private const VISITOR_TEST_IMPRESSION_COUNT = 5;
	private const VISITOR_TEST_BUCKET = 'test_bucket';
	private const VISITOR_TEST_DONATION_HISTORY = false;

	private function getTestbucket(): Bucket {
		return new Bucket(
			'test',
			new Banner( 'TestMain' )
		);
	}

	private function getTrueRandomTestCampaign(): Campaign {
		return new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2000-01-01 00:00:00' ),
			new \DateTime( '2099-12-31 23:59:59' ),
			1,
			new RandomInteger(),
			$this->getTestbucket()
		);
	}

	private function getTrueRandomTestCampaignCollection(): CampaignCollection {
		return new CampaignCollection(
			$this->getTrueRandomTestCampaign()
		);
	}

	private function getTestRequest(): Request {
		return new Request(
			[],
			[],
			[],
			[
				BannerSelectionController::IMPRESSION_COUNT_COOKIE => self::VISITOR_TEST_IMPRESSION_COUNT,
				BannerSelectionController::BUCKET_COOKIE => self::VISITOR_TEST_BUCKET,
				BannerSelectionController::DONATED_COOKIE => self::VISITOR_TEST_DONATION_HISTORY ]
		);
	}

	private function getTestVisitor(): Visitor {
		return new Visitor(
			self::VISITOR_TEST_IMPRESSION_COUNT,
			self::VISITOR_TEST_BUCKET,
			self::VISITOR_TEST_DONATION_HISTORY
		);
	}

	private function getFirstTimeVisitor(): Visitor {
		return new Visitor(
			0,
			null,
			false
		);
	}

	private function getMockAssets(): Packages {
		$mockAssets = $this->createMock( Packages::class );
		$mockAssets->method( 'getUrl' )->willReturnArgument( 0 );
		return $mockAssets;
	}

	public function test_given_controller_receives_cookies_values_are_passed_onto_use_case() {
		$mockUseCase = $this->createMock( BannerSelectionUseCase::class );
		$mockUseCase->expects( $this->once() )->method( 'provideBannerRequest' )->with( $this->getTestVisitor() );
		$controller = new BannerSelectionController( $mockUseCase, $this->getMockAssets() );
		$controller->selectBanner( $this->getTestRequest() );
	}

	public function test_given_no_cookies_passed_through_request_controller_assigns_default_values() {
		$mockUseCase = $this->createMock( BannerSelectionUseCase::class );
		$mockUseCase->expects( $this->once() )->method( 'provideBannerRequest' )->with( $this->getFirstTimeVisitor() );
		$controller = new BannerSelectionController( $mockUseCase, $this->getMockAssets() );
		$controller->selectBanner( new Request() );
	}

	public function test_given_no_cookies_passed_through_request_controller_creates_cookies_with_updated_default_values() {
		$testUseCase = new BannerSelectionUseCase(
			$this->getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( 10 )
		);
		$controller = new BannerSelectionController( $testUseCase, $this->getMockAssets() );
		$response = $controller->selectBanner( new Request() );

		$cookies = $response->headers->getCookies();

		$this->assertEquals( 2, count( $cookies ) );

		$this->assertEquals( BannerSelectionController::BUCKET_COOKIE, $cookies[0]->getName() );
		$this->assertEquals( 'test', $cookies[0]->getValue() );
		$this->assertEquals(
			( new \DateTime( '2099-12-31 23:59:59' ) )->modify( '+2 week' )->getTimestamp(),
			$cookies[0]->getExpiresTime(),
			'Cookie life-time should be the campaign expiration date plus two weeks.'
		);

		$this->assertEquals( BannerSelectionController::IMPRESSION_COUNT_COOKIE, $cookies[1]->getName() );
		$this->assertEquals( '1', $cookies[1]->getValue() );
		$this->assertEquals(
			( new \DateTime( '2099-12-31 23:59:59' ) )->modify( '+2 week' )->getTimestamp(),
			$cookies[1]->getExpiresTime(),
			'Cookie life-time should be the campaign expiration date plus two weeks.'
		);
	}

	public function test_given_banner_is_to_be_shown_redirect_response_is_returned() {
		$testUseCase = new BannerSelectionUseCase(
			$this->getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( 10 )
		);
		$controller = new BannerSelectionController( $testUseCase, $this->getMockAssets() );
		$response = $controller->selectBanner( new Request() );

		$this->assertInstanceOf( RedirectResponse::class, $response );
		$this->assertEquals( 'banners/TestMain.js', $response->headers->get( 'location' ) );
	}

	public function test_given_impression_limit_is_reached_no_content_response_is_returned() {
		$testUseCase = new BannerSelectionUseCase(
			$this->getTrueRandomTestCampaignCollection(),
			new ImpressionThreshold( self::VISITOR_TEST_IMPRESSION_COUNT )
		);
		$controller = new BannerSelectionController( $testUseCase, $this->getMockAssets() );
		$response = $controller->selectBanner( $this->getTestRequest() );

		$this->assertEquals( Response::HTTP_NO_CONTENT, $response->getStatusCode() );
	}
}
