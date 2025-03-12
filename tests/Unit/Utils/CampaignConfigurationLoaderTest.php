<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Unit\Utils;

use Closure;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Utils\CampaignConfigurationLoader;

#[CoversClass( CampaignConfigurationLoader::class )]
class CampaignConfigurationLoaderTest extends TestCase {

	private const TEST_VALID_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/campaigns.yml';
	private const TEST_BROKEN_BUCKET_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_bucket_campaign.yml';
	private const TEST_BROKEN_BANNER_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_banner_campaign.yml';
	private const TEST_BROKEN_DATA_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_data_campaign.yml';
	private const TEST_BROKEN_DISPLAYWIDTH_CAMPAIGN_CONFIGURATION_FILE = 'tests/Fixtures/campaigns/broken_displayWidth_campaign.yml';

	public function test_given_campaigns_are_loaded_then_loaded_campaign_data_is_correct(): void {
		$loader = new CampaignConfigurationLoader( self::TEST_VALID_CAMPAIGN_CONFIGURATION_FILE );
		$collection = $loader->getCampaignCollection();

		$campaign = $collection->getCampaign( new DateTime( '2018-12-12' ) );
		$categorizedCampaign = $collection->getCampaign( new DateTime( '2020-11-12' ) );

		$this->assertNotNull( $categorizedCampaign );
		$this->assertNotNull( $campaign );
		$this->assertEquals( 'B18WPDE_01_180131', $campaign->getIdentifier() );
		$this->assertEquals( '2019-01-01 14:00:00', $campaign->getEnd()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( 10, $campaign->getDisplayPercentage() );
		$this->assertEquals( 'default', $campaign->getCategory() );
		$this->assertEquals( 'fundraising_2020', $categorizedCampaign->getCategory() );

		$bucketA = $campaign->selectBucket( 'B18WPDE_01_180131_ctrl' );
		$bucketB = $campaign->selectBucket( 'B18WPDE_01_180131_var' );
		$this->assertEquals( 'B18WPDE_01_180131_ctrl', $bucketA->getIdentifier() );
		$this->assertEquals( 'B18WPDE_01_180131_var', $bucketB->getIdentifier() );

		$this->assertEquals( 'B18WPDE_01_180131_fulltop_ctrl', $bucketA->getBanner( 0 ) );
		$this->assertEquals( 'B18WPDE_01_180131_top_ctrl2', $bucketA->getBanner( 1 ) );
		$this->assertEquals( 'B18WPDE_02_180511_top_ctrl_last', $bucketA->getBanner( 5 ) );
		$this->assertEquals( 'B18WPDE_02_180511_top_ctrl_last', $bucketA->getBanner( 10 ) );
	}

	public function test_given_broken_bucket_campaign_configuration_then_errors_are_caught(): void {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_BUCKET_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'A configured bucket has no name.' );
		$loader->getCampaignCollection();
	}

	public function test_given_broken_banner_campaign_configuration_then_errors_are_caught(): void {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_BANNER_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'A configured bucket has no associated banners.' );
		$loader->getCampaignCollection();
	}

	public function test_given_missing_campaign_data_then_errors_are_caught(): void {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_DATA_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'Campaign data is incomplete.' );
		$loader->getCampaignCollection();
	}

	public function test_given_display_widths_max_is_larger_than_min_or_undefined(): void {
		$loader = new CampaignConfigurationLoader(
			self::TEST_BROKEN_DISPLAYWIDTH_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectExceptionMessage( 'Campaign data display width values are invalid (if defined, max must be higher than min)' );
		$loader->getCampaignCollection();
	}

	public function test_given_invalid_campaign_file_then_empty_campaign_configuration_is_returned(): void {
		$loader = new CampaignConfigurationLoader(
			'SOME_INVALID_PATH/' . self::TEST_VALID_CAMPAIGN_CONFIGURATION_FILE
		);
		$this->expectException( ParseException::class );
		$loader->getCampaignCollection();
	}

	public function test_given_empty_display_limits_they_are_set_to_null(): void {
		$loader = new CampaignConfigurationLoader( self::TEST_VALID_CAMPAIGN_CONFIGURATION_FILE );
		$collection = $loader->getCampaignCollection();
		// Using $campaign->isInDisplayRange instead of private property access wouldn't test reliably for null values,
		// adding getters for min/max width would break domain encapsulation, so we're cheating here in the test
		$readPrivateProperty = Closure::bind( static function ( Campaign $campaign, string $propertyName ) {
			return $campaign->$propertyName;
		}, null, Campaign::class );

		$campaign = $collection->getCampaign( new DateTime( '2020-11-26' ) );

		$this->assertNotNull( $campaign );
		$this->assertNull( $readPrivateProperty( $campaign, 'minDisplayWidth' ) );
		$this->assertNull( $readPrivateProperty( $campaign, 'maxDisplayWidth' ) );
	}
}
