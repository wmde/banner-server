<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;

/**
 * @covers \WMDE\BannerServer\Entity\BannerSelection\Bucket
 */
class BucketTest extends TestCase {

	public function test_given_first_time_visitor_then_main_banner_is_returned(): void {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' )
		);
		$this->assertEquals( 'C18_WMDE_Test_ctrl_main', $bucket->getBanner( 0 ) );
	}

	public function test_given_second_time_visitor_then_second_banner_is_returned(): void {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' )
		);
		$this->assertEquals( 'C18_WMDE_Test_ctrl_secondary', $bucket->getBanner( 1 ) );
	}

	public function test_given_third_time_visitor_then_other_banner_is_returned(): void {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' ),
			new Banner( 'C18_WMDE_Test_ctrl_tertiary' )
		);
		$this->assertEquals( 'C18_WMDE_Test_ctrl_tertiary', $bucket->getBanner( 2 ) );
	}

	public function test_given_regular_visitor_then_last_available_banner_is_returned(): void {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' ),
			new Banner( 'C18_WMDE_Test_ctrl_tertiary' )
		);
		$this->assertEquals( 'C18_WMDE_Test_ctrl_tertiary', $bucket->getBanner( 5 ) );
	}

	public function test_identifier_is_returned_correctly(): void {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' )
		);
		$this->assertEquals( 'C18_WMDE_Test_ctrl', $bucket->getIdentifier() );
	}
}
