<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\UseCase\BannerSelection;

use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\Entity\Visitor;
use WMDE\BannerServer\Tests\Fixtures\CampaignFixture;
use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\Tests\Utils\FakeRandomIntegerGenerator;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;
use WMDE\BannerServer\Utils\SystemRandomIntegerGenerator;

/**
 * @covers \WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase
 */
class BannerSelectionUseCaseTest extends TestCase {

	public function test_given_max_percentage_then_limit_is_not_applied(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 100 ),
			new ImpressionThreshold( 10 ),
			new SystemRandomIntegerGenerator()
		);

		$bannerSelectionData = $useCase->selectBanner( VisitorFixture::getFirstTimeVisitor() );
		$this->assertEquals( 'test', $bannerSelectionData->getVisitorData()->getBucketIdentifier() );
		$this->assertEquals( 1, $bannerSelectionData->getVisitorData()->getTotalImpressionCount() );
		$this->assertEquals( 'TestBanner', $bannerSelectionData->getBannerIdentifier() );
		$this->assertEquals( CampaignFixture::getTestCampaignEndDate(), $bannerSelectionData->getCampaignEnd() );
	}

	public function test_given_one_percent_ratio_then_limit_is_not_applied_for_one_percent_rng(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 1 ),
			new ImpressionThreshold( 10 ),
			new FakeRandomIntegerGenerator( 1 )
		);

		$bannerSelectionData = $useCase->selectBanner( VisitorFixture::getFirstTimeVisitor() );
		$this->assertEquals( 'test', $bannerSelectionData->getVisitorData()->getBucketIdentifier() );
		$this->assertEquals( 1, $bannerSelectionData->getVisitorData()->getTotalImpressionCount() );
		$this->assertEquals( 'TestBanner', $bannerSelectionData->getBannerIdentifier() );
		$this->assertEquals( CampaignFixture::getTestCampaignEndDate(), $bannerSelectionData->getCampaignEnd() );
	}

	public function test_given_one_percent_ratio_then_limit_is_applied_for_two_percent_rng(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 1 ),
			new ImpressionThreshold( 10 ),
			new FakeRandomIntegerGenerator( 2 )
		);

		$bannerSelectionData = $useCase->selectBanner( VisitorFixture::getFirstTimeVisitor() );
		$this->assertEquals( false, $bannerSelectionData->displayBanner() );
		$this->assertEquals( null, $bannerSelectionData->getVisitorData()->getBucketIdentifier() );
		$this->assertEquals( 0, $bannerSelectionData->getVisitorData()->getTotalImpressionCount() );
	}

	public function test_given_visitor_with_category_of_campaign_then_no_banner_is_shown(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 100 ),
			new ImpressionThreshold( 10 ),
			new SystemRandomIntegerGenerator()
		);
		$visitor = new Visitor( 0, null, CampaignFixture::TEST_CATEGORY, 'another-irrelevant-category' );

		$bannerSelectionData = $useCase->selectBanner( $visitor );

		$this->assertFalse( $bannerSelectionData->displayBanner(), 'No banner should be selected' );
		$this->assertEquals( null, $bannerSelectionData->getVisitorData()->getBucketIdentifier() );
		$this->assertEquals( 0, $bannerSelectionData->getVisitorData()->getTotalImpressionCount() );
	}

	public function test_when_banner_is_returned_then_view_count_is_incremented(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 100 ),
			new ImpressionThreshold( 10 ),
			new SystemRandomIntegerGenerator()
		);

		$bannerSelectionData = $useCase->selectBanner( VisitorFixture::getTestVisitor() );
		$this->assertEquals(
			VisitorFixture::VISITOR_TEST_IMPRESSION_COUNT + 1,
			$bannerSelectionData->getVisitorData()->getTotalImpressionCount()
		);
	}

	public function test_when_no_banner_is_returned_then_view_count_is_unchanged(): void {
		$useCase = new BannerSelectionUseCase(
			CampaignFixture::getTrueRandomTestCampaignCollection( 1 ),
			new ImpressionThreshold( 10 ),
			new FakeRandomIntegerGenerator( 2 )
		);

		$bannerSelectionData = $useCase->selectBanner( VisitorFixture::getTestVisitor() );
		$this->assertEquals(
			VisitorFixture::VISITOR_TEST_IMPRESSION_COUNT,
			$bannerSelectionData->getVisitorData()->getTotalImpressionCount()
		);
	}
}