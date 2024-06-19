<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;

#[CoversClass( ImpressionThreshold::class )]
class ImpressionThresholdTest extends TestCase {

	public function test_given_impression_value_then_correct_threshold_reached_state_is_returned(): void {
		$threshold = new ImpressionThreshold( 10 );
		$this->assertFalse( $threshold->isThresholdReached( 9 ) );
		$this->assertTrue( $threshold->isThresholdReached( 10 ) );
		$this->assertTrue( $threshold->isThresholdReached( 11 ) );
	}
}
