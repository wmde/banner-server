<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\UseCase\BannerSelection;

use WMDE\BannerServer\Tests\Fixtures\VisitorFixture;
use WMDE\BannerServer\UseCase\BannerSelection\ActiveBannerSelectionData;
use WMDE\BannerServer\UseCase\BannerSelection\Visitor;

/**
 * @covers \WMDE\BannerServer\UseCase\BannerSelection\Visitor
 * Class VisitorTest
 */
class VisitorTest extends \PHPUnit\Framework\TestCase {

	const TEST_IMPRESSION_COUNT = 2;
	const TEST_BUCKET = 'TEST_BUCKET123';

	public function test_given_visitor_who_has_donated_then_correct_values_are_returned() {
		$visitor = new Visitor( self::TEST_IMPRESSION_COUNT, self::TEST_BUCKET, true );
		$this->assertEquals( self::TEST_IMPRESSION_COUNT, $visitor->getTotalImpressionCount() );
		$this->assertEquals( self::TEST_BUCKET, $visitor->getBucketIdentifier() );
		$this->assertTrue( $visitor->hasDonated() );
	}

	public function test_given_visitor_who_has_not_donated_then_correct_values_are_returned() {
		$visitor = new Visitor( self::TEST_IMPRESSION_COUNT, self::TEST_BUCKET, false );
		$this->assertEquals( self::TEST_IMPRESSION_COUNT, $visitor->getTotalImpressionCount() );
		$this->assertEquals( self::TEST_BUCKET, $visitor->getBucketIdentifier() );
		$this->assertFalse( $visitor->hasDonated() );
	}
}
