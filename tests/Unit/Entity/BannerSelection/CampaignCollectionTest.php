<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use PHPUnit\Framework\TestCase;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Tests\Utils\FakeRandomIntegerGenerator;

/**
 * @covers \WMDE\BannerServer\Entity\BannerSelection\CampaignCollection
 */
class CampaignCollectionTest extends TestCase {

	private function getTestbucket(): Bucket {
		return new Bucket(
			'test',
			new Banner( 'TestBanner' )
		);
	}

	public function test_given_date_then_returns_active_campaign(): void {
		$campaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_future',
				new \DateTime( '2099-10-01 14:00:00' ),
				new \DateTime( '2099-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			)
		);

		$campaign = $campaignCollection->getCampaign( new \DateTime( '2018-10-22 13:59:59' ) );
		$this->assertNotNull( $campaign );
		$this->assertEquals( 'C18_WMDE_Test_present', $campaign->getIdentifier() );
	}

	public function test_given_date_out_of_range_then_does_not_return_campaign(): void {
		$campaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			)
		);

		$this->assertNull(
						$campaignCollection->getCampaign( new \DateTime( '2017-09-01 14:00:00' ) )
		);

		$this->assertNull( $campaignCollection->getCampaign( new \DateTime( '2017-09-01 14:00:00' ) ) );
	}

	public function test_filter_function_drops_invalid_campaigns(): void {
		$testCampaign1 = new Campaign(
			'C18_WMDE_Test_present',
			new \DateTime( '2018-10-01 14:00:00' ),
			new \DateTime( '2018-10-31 14:00:00' ),
			1,
			'default',
			new FakeRandomIntegerGenerator( 1 ),
			null,
			null,
			$this->getTestbucket()
		);
		$testCampaign2 = new Campaign(
			'C18_WMDE_Test_past',
			new \DateTime( '1999-10-01 14:00:00' ),
			new \DateTime( '1999-10-31 14:00:00' ),
			1,
			'default',
			new FakeRandomIntegerGenerator( 1 ),
			null,
			null,
			$this->getTestbucket()
		);

		$campaignCollection = new CampaignCollection(
			$testCampaign1,
			$testCampaign2
		);

		$filteredCampaignCollection = $campaignCollection->filter(
			static function ( Campaign $campaign ) {
				return $campaign->getIdentifier() === 'C18_WMDE_Test_past';
			}
			);

		$this->assertSame( $testCampaign2, $filteredCampaignCollection->getFirstCampaign() );
	}

	public function test_given_empty_collection_then_isEmpty_results_true(): void {
		$emptyCampaignCollection = new CampaignCollection();
		$this->assertTrue( $emptyCampaignCollection->isEmpty() );
	}

	public function test_given_non_empty_collection_then_isEmpty_results_false(): void {
		$nonEmptyCampaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				1,
				'default',
				new FakeRandomIntegerGenerator( 1 ),
				null,
				null,
				$this->getTestbucket()
			)
		);
		$this->assertFalse( $nonEmptyCampaignCollection->isEmpty() );
	}

	public function test_given_empty_collection_then_get_first_throws_exception(): void {
		$emptyCampaignCollection = new CampaignCollection();

		$this->expectException( 'OutOfBoundsException' );
		$emptyCampaignCollection->getFirstCampaign();
	}
}
