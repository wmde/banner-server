<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;

/**
 * @covers \WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold
 * Class ImpressionThresholdTest
 */
class ImpressionThresholdTest extends \PHPUnit\Framework\TestCase {

	const BANNER_TEST_IDENTIFIER = 'testIdentifier';

	public function test_given_impression_value_then_correct_threshold_reached_state_is_returned() {
		$threshold = new ImpressionThreshold( 10 );
		$this->assertFalse( $threshold->isThresholdReached( 9 ) );
		$this->assertTrue( $threshold->isThresholdReached( 10 ) );
		$this->assertTrue( $threshold->isThresholdReached( 11 ) );
	}
}
