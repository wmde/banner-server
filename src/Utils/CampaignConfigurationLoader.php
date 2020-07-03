<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

use Symfony\Component\Yaml\Yaml;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;

/**
 * @license GPL-2.0-or-later
 */
class CampaignConfigurationLoader {

	private $configFile;

	public function __construct( string $configFile ) {
		$this->configFile = $configFile;
	}

	/**
	 * @throws \Exception
	 * @throws \DomainException
	 * @throws \Symfony\Component\Yaml\Exception\ParseException
	 */
	public function getCampaignCollection(): CampaignCollection {
		$campaigns = [];
		foreach ( $this->parseConfiguration() as $campaignName => $campaignData ) {
				$campaign = $this->buildCampaignFromData( $campaignName, $campaignData );
				$campaigns[] = $campaign;
		}
		return new CampaignCollection( ...$campaigns );
	}

	/**
	 * @throws \Exception
	 * @throws \DomainException
	 */
	private function buildCampaignFromData( string $campaignName, array $campaignData ): Campaign {
		$buckets = $this->buildBucketsFromData( $campaignData );
		if ( empty( $buckets ) ) {
			throw new \DomainException( 'Campaign contains no buckets.' );
		}
		if ( empty( $campaignData['start'] ) || empty( $campaignData['end'] ) || !isset( $campaignData['trafficLimit'] ) ) {
			throw new \DomainException( 'Campaign data is incomplete.' );
		}
		if ( is_numeric( $campaignData['minDisplayWidth'] ) && is_numeric( $campaignData['maxDisplayWidth'] ) ) {
			if ( $campaignData['minDisplayWidth'] > $campaignData['maxDisplayWidth'] ) {
				throw new \DomainException(
					'Campaign data display width values are invalid (if defined, max must be higher than min).'
				);
			}
		}
		return new Campaign(
			$campaignName,
			new \DateTime( $campaignData['start'] ),
			new \DateTime( $campaignData['end'] ),
			(int)$campaignData['trafficLimit'],
			$campaignData['category'] ?? 'default',
			new SystemRandomIntegerGenerator(),
			(int)$campaignData['minDisplayWidth'],
			(int)$campaignData['maxDisplayWidth'],
			array_shift( $buckets ),
			...$buckets
		);
	}

	/**
	 * @throws \DomainException
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
	 * @throws \DomainException
	 */
	private function buildBucketFromData( array $bucketData ): Bucket {
		if ( !isset( $bucketData['name'] ) ) {
			throw new \DomainException( 'A configured bucket has no name.' );
		}
		if ( !isset( $bucketData['banners'] ) ) {
			throw new \DomainException( 'A configured bucket has no associated banners.' );
		}
		$banners = $this->buildBannersFromData( $bucketData['banners'] );
		if ( empty( $banners ) ) {
			throw new \DomainException( 'A configured bucket has no valid banners associated with it.' );
		}
		return new Bucket( $bucketData['name'], array_shift( $banners ), ...$banners );
	}

	/**
	 * @throws \DomainException
	 */
	private function buildBannersFromData( array $bannerData ): array {
		$banners = [];
		foreach ( $bannerData as $bannerIdentifier ) {
			if ( !$bannerIdentifier ) {
				throw new \DomainException( 'A configured banner has an empty name.' );
			}
			$banners[] = new Banner( $bannerIdentifier );
		}
		return $banners;
	}

	/**
	 * @throws \Symfony\Component\Yaml\Exception\ParseException
	 */
	private function parseConfiguration(): array {
		return Yaml::parseFile( $this->configFile );
	}
}
