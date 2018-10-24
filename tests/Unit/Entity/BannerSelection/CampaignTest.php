<?php

declare(strict_types = 1);

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;

class CampaignTest extends \PHPUnit\Framework\TestCase {

	private function createBuckets(): array {
		$buckets = [];
		$buckets[] = new Bucket(
			'C18_WMDE_Test_ctrl',
			new Banner( 'C18_WMDE_Test_ctrl_main' ),
			new Banner( 'C18_WMDE_Test_ctrl_secondary' )
		);
		$buckets[] = new Bucket(
			'C18_WMDE_Test_var',
			new Banner( 'C18_WMDE_Test_var_main' ),
			new Banner( 'C18_WMDE_Test_var_secondary' )
		);
		return $buckets;
	}

	public static function returnRandomBucket( array $buckets ): Bucket {
		return $buckets[0];
	}

	public function test_given_time_out_of_date_range_campaign_is_not_active() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			$this->createBuckets()
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

	public function test_given_time_in_the_date_range_campaign_is_active() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			$this->createBuckets()
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

	public function test_given_valid_bucket_id_returns_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			$this->createBuckets()
		);
		$this->assertEquals(
			$campaign->selectBucket( 'C18_WMDE_Test_var', [ self::class, 'returnRandomBucket' ] )->getIdentifier(),
			'C18_WMDE_Test_var'
		);
	}

	public function test_given_invalid_bucket_id_returns_random_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			$this->createBuckets()
		);
		$this->assertEquals(
			$campaign->selectBucket( 'C18_WMDE_Test_var_666', [ self::class, 'returnRandomBucket' ] )->getIdentifier(),
			'C18_WMDE_Test_ctrl'
		);
	}

	public function test_given_no_bucket_id_returns_random_bucket() {
		$campaign = new Campaign(
			'C18_WMDE_Test',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			$this->createBuckets()
		);
		$this->assertEquals(
			$campaign->selectBucket( null, [ self::class, 'returnRandomBucket' ] )->getIdentifier(),
			'C18_WMDE_Test_ctrl'
		);
	}
}
