<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Fixtures;

use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Utils\SystemRandomIntegerGenerator;

class CampaignFixture {

	public static function getTestCampaignStartDate(): \DateTime {
		return new \DateTime( '2000-01-01 00:00:00' );
	}

	public static function getTestCampaignEndDate(): \DateTime {
		return new \DateTime( '2099-12-31 23:59:59' );
	}

	public static function getTrueRandomTestCampaign( int $displayPercentage = 100 ): Campaign {
		return new Campaign(
			'C18_WMDE_Test',
			self::getTestCampaignStartDate(),
			self::getTestCampaignEndDate(),
			$displayPercentage,
			'default',
			new SystemRandomIntegerGenerator(),
			BucketFixture::getTestBucket()
		);
	}

	public static function getTrueRandomTestCampaignCollection( int $displayPercentage = 100 ): CampaignCollection {
		return new CampaignCollection(
			self::getTrueRandomTestCampaign( $displayPercentage )
		);
	}
}
