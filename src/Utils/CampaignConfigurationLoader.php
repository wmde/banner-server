<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

use Symfony\Component\Yaml\Yaml;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\InvalidConfigurationValueException;

class CampaignConfigurationLoader {

	private string $configFile;

	public function __construct( string $configFile ) {
		$this->configFile = $configFile;
	}

	public function getCampaignCollection(): CampaignCollection {
		$campaigns = [];
		$configuration = $this->parseConfiguration();

		foreach ( $configuration as $campaignName => $campaignData ) {
			if ( !is_array( $campaignData ) ) {
				throw new InvalidConfigurationValueException(
					"Campaign data for '{$campaignName}' must be an array."
				);
			}
			$campaign = $this->buildCampaignFromData( $campaignName, $campaignData );
			$campaigns[] = $campaign;
		}
		return new CampaignCollection( ...$campaigns );
	}

	/**
	 * @param array<string, mixed> $data
	 * @param string $key
	 * @return string
	 * @throws InvalidConfigurationValueException
	 */
	private function getStringValue( array $data, string $key ): string {
		if ( !isset( $data[$key] ) || !is_scalar( $data[$key] ) ) {
			throw new InvalidConfigurationValueException( "Configuration value for '$key' must be a string." );
		}
		return (string)$data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 * @param string $key
	 * @return int
	 * @throws InvalidConfigurationValueException
	 */
	private function getIntValue( array $data, string $key ): int {
		if ( !isset( $data[$key] ) || !is_numeric( $data[$key] ) ) {
			throw new InvalidConfigurationValueException( "Configuration value for '$key' must be numeric." );
		}
		return (int)$data[$key];
	}

	/**
	 * @param string $campaignName
	 * @param array<string,mixed> $campaignData
	 */
	private function buildCampaignFromData( string $campaignName, array $campaignData ): Campaign {
		$start = $this->getStringValue( $campaignData, 'start' );
		$end   = $this->getStringValue( $campaignData, 'end' );

		if ( !isset( $campaignData['trafficLimit'] ) ) {
			throw new InvalidConfigurationValueException( 'Campaign data is incomplete.' );
		}
		$trafficLimit = $this->getIntValue( $campaignData, 'trafficLimit' );

		$buckets = $this->buildBucketsFromData( $campaignData );
		if ( empty( $buckets ) ) {
			throw new InvalidConfigurationValueException( 'Campaign contains no buckets.' );
		}

		$minDisplayWidth = $this->integerOrNullValue( $campaignData, 'minDisplayWidth' );
		$maxDisplayWidth = $this->integerOrNullValue( $campaignData, 'maxDisplayWidth' );

		if ( $minDisplayWidth && $maxDisplayWidth && $minDisplayWidth > $maxDisplayWidth ) {
			throw new InvalidConfigurationValueException(
				'Campaign data display width values are invalid (if defined, max must be higher than min).'
			);
		}

		$category = isset( $campaignData['category'] ) && is_scalar( $campaignData['category'] )
			? (string)$campaignData['category']
			: 'default';

		return new Campaign(
			$campaignName,
			new \DateTime( $start ),
			new \DateTime( $end ),
			$trafficLimit,
			$category,
			new SystemRandomIntegerGenerator(),
			$minDisplayWidth,
			$maxDisplayWidth,
			array_shift( $buckets ),
			...$buckets
		);
	}

	/**
	 * @param array<string,mixed> $campaignData
	 * @return Bucket[]
	 */
	private function buildBucketsFromData( array $campaignData ): array {
		if ( !isset( $campaignData['buckets'] ) || !is_array( $campaignData['buckets'] ) ) {
			throw new InvalidConfigurationValueException( 'Campaign buckets must be defined as an array.' );
		}

		$buckets = [];
		foreach ( $campaignData['buckets'] as $bucketData ) {
			$buckets[] = $this->buildBucketFromData( $bucketData );
		}
		return $buckets;
	}

	/**
	 * @param array<string,mixed> $bucketData
	 * @return Bucket
	 */
	private function buildBucketFromData( array $bucketData ): Bucket {
		if ( !isset( $bucketData['name'] ) ) {
			throw new InvalidConfigurationValueException( 'A configured bucket has no name.' );
		}

		$bucketName = $this->getStringValue( $bucketData, 'name' );
		if ( !isset( $bucketData['banners'] ) || !is_array( $bucketData['banners'] ) ) {
			throw new InvalidConfigurationValueException( 'A configured bucket has no associated banners.' );
		}
		$banners = $this->buildBannersFromData( $bucketData['banners'] );
		if ( empty( $banners ) ) {
			throw new InvalidConfigurationValueException( 'A configured bucket has no valid banners associated with it.' );
		}
		return new Bucket( $bucketName, array_shift( $banners ), ...$banners );
	}

	/**
	 * @param string[] $bannerData
	 * @return Banner[]
	 */
	private function buildBannersFromData( array $bannerData ): array {
		$banners = [];
		foreach ( $bannerData as $bannerIdentifier ) {
			if ( !is_string( $bannerIdentifier ) || !$bannerIdentifier ) {
				throw new InvalidConfigurationValueException( 'A configured banner has an invalid or empty name.' );
			}
			$banners[] = new Banner( $bannerIdentifier );
		}
		return $banners;
	}

	/**
	 * @return array<string,mixed>
	 */
	private function parseConfiguration(): array {
		$config = Yaml::parseFile( $this->configFile );
		if ( !is_array( $config ) ) {
			throw new InvalidConfigurationValueException( 'Configuration file must return an array.' );
		}
		return $config;
	}

	/**
	 * @param array<string,mixed> $campaignData
	 */
	private function integerOrNullValue( array $campaignData, string $key ): ?int {
		if ( !isset( $campaignData[$key] ) ) {
			return null;
		}
		$value = $campaignData[$key];
		if ( !is_numeric( $value ) ) {
			return null;
		}
		return intval( $value );
	}
}
