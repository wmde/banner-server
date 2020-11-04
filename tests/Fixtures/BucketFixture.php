<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Fixtures;

use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;

class BucketFixture {
	public const TEST_BUCKET_IDENTIFIER = 'test';
	public const TEST_BANNER_IDENTIFIER = 'TestBanner';

	public static function getTestbucket( ?string $bannerIdentifier = null ): Bucket {
		return new Bucket(
			self::TEST_BUCKET_IDENTIFIER,
			new Banner( $bannerIdentifier ?? self::TEST_BANNER_IDENTIFIER )
		);
	}
}
