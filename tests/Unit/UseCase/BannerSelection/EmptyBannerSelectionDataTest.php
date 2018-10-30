<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\UseCase\BannerSelection\ActiveBannerSelectionData;
use WMDE\BannerServer\UseCase\BannerSelection\EmptyBannerSelectionData;

/**
 * @covers \WMDE\BannerServer\UseCase\BannerSelection\EmptyBannerSelectionData
 * Class EmptyBannerSelectionDataTest
 */
class EmptyBannerSelectionDataTest extends \PHPUnit\Framework\TestCase {

	const BANNER_TEST_IDENTIFIER = 'testIdentifier';
	const TEST_DATETIME = '19-06-1992 01:02:03';

	public function test_given_inactive_campaign_for_visitor_then_banner_is_not_displayed() {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->assertFalse( $bannerSelectionData->displayBanner() );
		$this->assertEquals( VisitorFixture::getTestVisitor(), $bannerSelectionData->getVisitorData() );
	}

	public function test_given_inactive_campaign_for_visitor_then_get_campaign_end_throws_exception() {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->expectException( 'LogicException' );
		$bannerSelectionData->getCampaignEnd();
	}

	public function test_given_inactive_campaign_for_visitor_then_get_banner_identifier_throws_exception() {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->expectException( 'LogicException' );
		$bannerSelectionData->getBannerIdentifier();
	}
}
