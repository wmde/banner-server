<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Utils;

use Symfony\Component\Yaml\Exception\ParseException;
use WMDE\BannerServer\Utils\CampaignConfigurationLoader;

/**
 * @covers \WMDE\BannerServer\Utils\CampaignConfigurationLoader
 * Class CampaignConfigurationLoaderTest
 */
class CampaignConfigurationLoaderTest extends \PHPUnit\Framework\TestCase {

	const TEST_VALID_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/campaigns.yml';
	const TEST_BROKEN_BUCKET_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_bucket_campaign.yml';
	const TEST_BROKEN_BANNER_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_banner_campaign.yml';
	const TEST_BROKEN_DATA_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_data_campaign.yml';

	public function test_given_campaigns_are_loaded_then_loaded_campaign_data_is_correct() {
		$loader = new CampaignConfigurationLoader( self::TEST_VALID_CAMPAIGN_CONFIGURATION_FILE );
		$collection = $loader->getCampaignCollection();

		$campaign = $collection->getCampaign( new \DateTime( '2018-12-12' ) );
		$this->assertNotNull( $campaign );
		$this->assertEquals( 'B18WPDE_01_180131', $campaign->getIdentifier() );
		$this->assertEquals( '2019-01-01 14:00:00', $campaign->getEnd()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( 10, $campaign->getDisplayPercentage() );

		$bucketA = $campaign->selectBucket( 'B18WPDE_01_180131_ctrl' );
		$bucketB = $campaign->selectBucket( 'B18WPDE_01_180131_var' );
		$this->assertEquals( $bucketA->getIdentifier(), 'B18WPDE_01_180131_ctrl' );
		$this->assertEquals( $bucketB->getIdentifier(), 'B18WPDE_01_180131_var' );

		$this->assertEquals( 'B18WPDE_01_180131_fulltop_ctrl', $bucketA->getBanner( 0 ) );
		$this->assertEquals( 'B18WPDE_01_180131_top_ctrl2', $bucketA->getBanner( 1 ) );
		$this->assertEquals( 'B18WPDE_02_180511_top_ctrl_last', $bucketA->getBanner( 5 ) );
		$this->assertEquals( 'B18WPDE_02_180511_top_ctrl_last', $bucketA->getBanner( 10 ) );
	}

	public function test_given_broken_bucket_campaign_configuration_then_errors_are_caught() {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_BUCKET_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'A configured bucket has no name.' );
		$loader->getCampaignCollection();
	}

	public function test_given_broken_banner_campaign_configuration_then_errors_are_caught() {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_BANNER_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'A configured bucket has no associated banners.' );
		$loader->getCampaignCollection();
	}

	public function test_given_missing_campaign_data_then_errors_are_caught() {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_DATA_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'Campaign data is incomplete.' );
		$loader->getCampaignCollection();
	}

	public function test_given_invalid_campaign_file_then_empty_campaign_configuration_is_returned() {
		$loader = new CampaignConfigurationLoader(
			'SOME_INVALID_PATH/' . self::TEST_VALID_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectException( ParseException::class );
		$loader->getCampaignCollection();
	}
}
