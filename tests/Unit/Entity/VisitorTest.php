<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\Visitor;

/**
 * @covers \WMDE\BannerServer\Entity\Visitor
 */
class VisitorTest extends TestCase {

	const TEST_IMPRESSION_COUNT = 2;
	const TEST_BUCKET = 'TEST_BUCKET123';

	public function test_given_visitor_without_categories_then_correct_values_are_returned(): void {
		$visitor = new Visitor( self::TEST_IMPRESSION_COUNT, self::TEST_BUCKET );
		$this->assertEquals( self::TEST_IMPRESSION_COUNT, $visitor->getTotalImpressionCount() );
		$this->assertEquals( self::TEST_BUCKET, $visitor->getBucketIdentifier() );
		$this->assertFalse( $visitor->inCategory( 'default' ) );
	}

	public function test_given_visitor_with_categories_then_correct_values_are_returned(): void {
		$visitor = new Visitor( self::TEST_IMPRESSION_COUNT, self::TEST_BUCKET, 'default', 'fundraising_2020' );
		$this->assertTrue( $visitor->inCategory( 'default' ) );
		$this->assertTrue( $visitor->inCategory( 'fundraising_2020' ) );
		$this->assertFalse( $visitor->inCategory( 'dummy_category' ) );
	}
}