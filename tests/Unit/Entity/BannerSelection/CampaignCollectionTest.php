<?php

declare(strict_types = 1);

namespace WMDE\BannerServer\Tests\Unit\Entity\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;

class CampaignCollectionTest extends \PHPUnit\Framework\TestCase {

	public function test_given_date_returns_active_campaign() {
		$campaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_future',
				new \DateTime( '2099-10-01 14:00:00' ),
				new \DateTime( '2099-10-31 14:00:00' ),
				[]
			),
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				[]
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				[]
			)
		);
		$this->assertEquals(
			$campaignCollection->getCampaign( new \DateTime( '2018-10-22 13:59:59' ) )->getIdentifier(),
			'C18_WMDE_Test_present'
		);
	}

	public function test_given_date_out_of_range_does_not_return_campaign() {
		$campaignCollection = new CampaignCollection(
			new Campaign(
				'C18_WMDE_Test_present',
				new \DateTime( '2018-10-01 14:00:00' ),
				new \DateTime( '2018-10-31 14:00:00' ),
				[]
			),
			new Campaign(
				'C18_WMDE_Test_past',
				new \DateTime( '1999-10-01 14:00:00' ),
				new \DateTime( '1999-10-31 14:00:00' ),
				[]
			)
		);
		$this->assertNull( $campaignCollection->getCampaign( new \DateTime( '2017-09-01 14:00:00' ) ) );
	}
}
