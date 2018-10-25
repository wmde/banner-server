<?php

declare(strict_types = 1);

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;

class BucketTest extends \PHPUnit\Framework\TestCase {

	public function test_given_first_time_visitor_main_banner_is_returned() {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			[new Banner( 'C18_WMDE_Test_ctrl_secondary' )]
		);
		$this->assertEquals( $bucket->getBanner( 0 ), 'C18_WMDE_Test_ctrl_main' );
	}

	public function test_given_not_first_time_visitor_other_banner_is_returned() {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			[new Banner( 'C18_WMDE_Test_ctrl_secondary' )]
		);
		$this->assertEquals( $bucket->getBanner( 1 ), 'C18_WMDE_Test_ctrl_secondary' );
	}

	public function test_given_regular_visitor_last_available_banner_is_returned() {
		$bucket = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			[new Banner( 'C18_WMDE_Test_ctrl_secondary' )]
		);
		$this->assertEquals( $bucket->getBanner( 5 ), 'C18_WMDE_Test_ctrl_secondary' );
	}
}
