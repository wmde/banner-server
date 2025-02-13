<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

use Symfony\Component\Yaml\Yaml;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\InvalidConfigurationValueException;

/**
 * @license GPL-2.0-or-later
 */
class CampaignConfigurationLoader {

	private string $configFile;

	public function __construct( string $configFile ) {
		$this->configFile = $configFile;
	}

	public function getCampaignCollection(): CampaignCollection {
		$campaigns = [];
		foreach ( $this->parseConfiguration() as $campaignName => $campaignData ) {
				$campaign = $this->buildCampaignFromData( $campaignName, $campaignData );
				$campaigns[] = $campaign;
		}
		return new CampaignCollection( ...$campaigns );
	}

	/**
	 * @param string $campaignName
	 * @param array<string,mixed> $campaignData
	 */
	private function buildCampaignFromData( string $campaignName, array $campaignData ): Campaign {
		$buckets = $this->buildBucketsFromData( $campaignData );
		if ( empty( $buckets ) ) {
			throw new InvalidConfigurationValueException( 'Campaign contains no buckets.' );
		}
		if ( empty( $campaignData['start'] ) || empty( $campaignData['end'] ) || !isset( $campaignData['trafficLimit'] ) ) {
			throw new InvalidConfigurationValueException( 'Campaign data is incomplete.' );
		}
		$minDisplayWidth = $this->integerOrNullValue( $campaignData, 'minDisplayWidth' );
		$maxDisplayWidth = $this->integerOrNullValue( $campaignData, 'maxDisplayWidth' );
		if ( $minDisplayWidth && $maxDisplayWidth && $minDisplayWidth > $maxDisplayWidth ) {
			throw new InvalidConfigurationValueException(
				'Campaign data display width values are invalid (if defined, max must be higher than min).'
			);
		}
		return new Campaign(
			$campaignName,
			new \DateTime( $campaignData['start'] ),
			new \DateTime( $campaignData['end'] ),
			(int)$campaignData['trafficLimit'],
			$campaignData['category'] ?? 'default',
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
		$buckets = [];
		foreach ( $campaignData['buckets'] as $bucketData ) {
			$bucket = $this->buildBucketFromData( $bucketData );
			$buckets[] = $bucket;
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
		if ( !isset( $bucketData['banners'] ) ) {
			throw new InvalidConfigurationValueException( 'A configured bucket has no associated banners.' );
		}
		$banners = $this->buildBannersFromData( $bucketData['banners'] );
		if ( empty( $banners ) ) {
			throw new InvalidConfigurationValueException( 'A configured bucket has no valid banners associated with it.' );
		}
		return new Bucket( $bucketData['name'], array_shift( $banners ), ...$banners );
	}

	/**
	 * @param string[] $bannerData
	 * @return Banner[]
	 */
	private function buildBannersFromData( array $bannerData ): array {
		$banners = [];
		foreach ( $bannerData as $bannerIdentifier ) {
			if ( !$bannerIdentifier ) {
				throw new InvalidConfigurationValueException( 'A configured banner has an empty name.' );
			}
			$banners[] = new Banner( $bannerIdentifier );
		}
		return $banners;
	}

	/**
	 * @return array<string,mixed>
	 */
	private function parseConfiguration(): array {
		return Yaml::parseFile( $this->configFile );
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
