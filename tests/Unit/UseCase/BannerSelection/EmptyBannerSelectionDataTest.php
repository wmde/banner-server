<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\UseCase\BannerSelection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\UseCase\BannerSelection\EmptyBannerSelectionData;

#[CoversClass( EmptyBannerSelectionData::class )]
class EmptyBannerSelectionDataTest extends TestCase {

	public function test_given_inactive_campaign_for_visitor_then_banner_is_not_displayed(): void {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->assertFalse( $bannerSelectionData->displayBanner() );
		$this->assertEquals( VisitorFixture::getTestVisitor(), $bannerSelectionData->getVisitorData() );
	}

	public function test_given_inactive_campaign_for_visitor_then_get_campaign_end_throws_exception(): void {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->expectException( 'LogicException' );
		$bannerSelectionData->getCampaignEnd();
	}

	public function test_given_inactive_campaign_for_visitor_then_get_banner_identifier_throws_exception(): void {
		$bannerSelectionData = new EmptyBannerSelectionData(
			VisitorFixture::getTestVisitor()
		);
		$this->expectException( 'LogicException' );
		$bannerSelectionData->getBannerIdentifier();
	}
}
