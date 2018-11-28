<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Tests\Utils\FakeRandomIntegerGenerator;

/**
 * @covers \WMDE\BannerServer\Entity\BannerSelection\Campaign
 */
class CampaignTest extends \PHPUnit\Framework\TestCase {

	private function getControlBucket(): Bucket {
		return new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' )
		);
	}

	private function getVariantBucket(): Bucket {
		return new Bucket(
			'C18_WMDE_Test_var',
			new Banner( 'C18_WMDE_Test_var_main' ),
			new Banner( 'C18_WMDE_Test_var_secondary' )
		);
	}

	public function test_given_time_out_of_date_range_then_campaign_is_not_active() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertTrue(
			$campaign->isInActiveDateRange( new \DateTime( '2018-10-01 14:00:00' ) ),
			'date given is exactly the start date of the campaign'
		);
		$this->assertTrue(
			$campaign->isInActiveDateRange( new \DateTime( '2018-10-20 14:00:00' ) ),
			'date given is between the start and the end of the campaign'
		);
		$this->assertTrue(
			$campaign->isInActiveDateRange( new \DateTime( '2018-10-31 14:00:00' ) ),
			'date given is exactly the end date of the campaign'
		);

	}

	public function test_given_time_in_the_date_range_then_campaign_is_active() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertFalse(
			$campaign->isInActiveDateRange( new \DateTime( '2018-09-22 14:00:00' ) ),
			'date given is before the start of the campaign'
		);
		$this->assertFalse(
			$campaign->isInActiveDateRange( new \DateTime( '2018-10-01 13:59:59' ) ),
			'date given is before the start of the campaign'
		);
		$this->assertFalse(
			$campaign->isInActiveDateRange( new \DateTime( '2018-10-31 14:00:01' ) ),
			'date given is after the end of the campaign'
		);
	}

	public function test_given_valid_bucket_id_then_returns_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertEquals(
			$campaign->selectBucket( 'C18_WMDE_Test_var' )->getIdentifier(),
			'C18_WMDE_Test_var'
		);
	}

	public function test_given_invalid_bucket_id_then_returns_random_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertEquals(
			$campaign->selectBucket( 'C18_WMDE_Test_var_666' )->getIdentifier(),
			'C18_WMDE_Test_var'
		);
	}

	public function test_given_no_bucket_id_then_returns_random_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertEquals(
			$campaign->selectBucket( null )->getIdentifier(),
			'C18_WMDE_Test_var'
		);
	}

	public function test_getters_return_correct_values() {
		$identifier = 'C18_WMDE_Test';
		$displayPercentage = 12;
		$endDate = new \DateTime( '2018-10-31 14:00:00' );
		$campaign = new Campaign(
			$identifier,
			new \DateTime( '2018-10-01 14:00:00' ),
			$endDate,
			$displayPercentage,
			new FakeRandomIntegerGenerator( 1 ),
			$this->getControlBucket(),
			$this->getVariantBucket()
		);
		$this->assertEquals( $identifier, $campaign->getIdentifier() );
		$this->assertEquals( $displayPercentage, $campaign->getDisplayPercentage() );
		$this->assertEquals( $endDate, $campaign->getEnd() );
	}
}
