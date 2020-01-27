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
				new FakeRandomIntegerGenerator( 1 ),
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				1,
				new FakeRandomIntegerGenerator( 1 ),
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				1,
				new FakeRandomIntegerGenerator( 1 ),
				$this->getTestbucket()
			)
		);

		$campaign = $campaignCollection->getCampaign( new \DateTime( '2018-10-22 13:59:59' ) );
		$this->assertNotNull( $campaign );
		$this->assertEquals( $campaign->getIdentifier(), 'C18_WMDE_Test_present' );
	}

	public function test_given_date_out_of_range_then_does_not_return_campaign(): void {
		$campaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				1,
				new FakeRandomIntegerGenerator( 1 ),
				$this->getTestbucket()
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				1,
				new FakeRandomIntegerGenerator( 1 ),
				$this->getTestbucket()
			)
		);

		$this->assertEquals(
			$campaignCollection->getCampaign( new \DateTime( '2017-09-01 14:00:00' ) ),
			null
		);

		$this->assertNull( $campaignCollection->getCampaign( new \DateTime( '2017-09-01 14:00:00' ) ) );
	}
}
