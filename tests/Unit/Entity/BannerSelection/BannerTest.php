<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Banner;

/**
 * @covers \WMDE\BannerServer\Entity\BannerSelection\Banner
 */
class BannerTest extends \PHPUnit\Framework\TestCase {

	const BANNER_TEST_IDENTIFIER = 'testIdentifier';

	public function test_banner_returns_proper_identifier(): void {
		$banner = new Banner( self::BANNER_TEST_IDENTIFIER );
		$this->assertEquals( self::BANNER_TEST_IDENTIFIER, $banner->getIdentifier() );
	}
}
