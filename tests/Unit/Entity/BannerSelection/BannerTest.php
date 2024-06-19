<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\BannerSelection\Banner;

#[CoversClass( Banner::class )]
class BannerTest extends TestCase {

	private const BANNER_TEST_IDENTIFIER = 'testIdentifier';

	public function test_banner_returns_proper_identifier(): void {
		$banner = new Banner( self::BANNER_TEST_IDENTIFIER );
		$this->assertEquals( self::BANNER_TEST_IDENTIFIER, $banner->getIdentifier() );
	}
}
