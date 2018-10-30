<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\UseCase\BannerSelection\ActiveBannerSelectionData;

/**
 * @covers \WMDE\BannerServer\UseCase\BannerSelection\ActiveBannerSelectionData
 * Class ActiveBannerSelectionDataTest
 */
class ActiveBannerSelectionDataTest extends \PHPUnit\Framework\TestCase {

	const BANNER_TEST_IDENTIFIER = 'testIdentifier';
	const TEST_DATETIME = '19-06-1992 01:02:03';

	public function test_given_active_campaign_for_visitor_then_correct_values_are_stored_and_returned() {
		$bannerSelectionData = new ActiveBannerSelectionData(
			VisitorFixture::getTestVisitor(),
			self::BANNER_TEST_IDENTIFIER,
			new \DateTime( self::TEST_DATETIME )
		);
		$this->assertTrue( $bannerSelectionData->displayBanner() );
		$this->assertEquals( new \DateTime( self::TEST_DATETIME ), $bannerSelectionData->getCampaignEnd() );
		$this->assertEquals( self::BANNER_TEST_IDENTIFIER, $bannerSelectionData->getBannerIdentifier() );
		$this->assertEquals( VisitorFixture::getTestVisitor(), $bannerSelectionData->getVisitorData() );
	}
}
