<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use WMDE\BannerServer\Entity\BannerSelection\Banner;
use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Utils\SystemRandomIntegerGenerator;

/**
 * @license GNU GPL v2+
 */
class CampaignConfigurationLoader {

	private $configFile;
	private $logger;

	public function __construct( LoggerInterface $logger, string $configFile ) {
		$this->configFile = $configFile;
		$this->logger = $logger;
	}

	public function getCampaignCollection(): CampaignCollection {
		$campaigns = [];
		foreach ( $this->parseConfiguration() as $campaignName => $campaignData ) {
			try {
				$campaign = $this->buildCampaignFromData( $campaignName, $campaignData );
				$campaigns[] = $campaign;
			}
			catch ( \Exception $e ) {
				$this->logger->critical( $e->getMessage(), [ 'exception' => $e ] );
			}
		}
		return new CampaignCollection( ...$campaigns );
	}

	/**
	 * @throws \Exception
	 */
	private function buildCampaignFromData( string $campaignName, array $campaignData ): Campaign {
		$buckets = $this->buildBucketsFromData( $campaignData );
		if ( empty( $buckets ) ) {
			throw new \Exception( 'Campaign contains no buckets.' );
		}
		if ( empty( $campaignData['start'] ) || empty( $campaignData['end'] ) || !isset( $campaignData['trafficLimit'] ) ) {
			throw new \Exception( 'Campaign data is incomplete.' );
		}
		return new Campaign(
			$campaignName,
			new \DateTime( $campaignData['start'] ),
			new \DateTime( $campaignData['end'] ),
			(int)$campaignData['trafficLimit'],
			new SystemRandomIntegerGenerator(),
			array_shift( $buckets ),
			...$buckets
		);
	}

	/**
	 * @throws \Exception
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
	 * @throws \Exception
	 */
	private function buildBucketFromData( array $bucketData ): Bucket {
		if ( !isset( $bucketData['name'] ) ) {
			throw new \Exception( 'A configured bucket has no name.' );
		}
		if ( !isset( $bucketData['banners'] ) ) {
			throw new \Exception( 'A configured bucket has no associated banners.' );
		}
		$banners = $this->buildBannersFromData( $bucketData['banners'] );
		if ( empty( $banners ) ) {
			throw new \Exception( 'A configured bucket has no valid banners associated with it.' );
		}
		return new Bucket( $bucketData['name'], array_shift( $banners ), ...$banners );
	}

	/**
	 * @throws \Exception
	 */
	private function buildBannersFromData( array $bannerData ): array {
		$banners = [];
		foreach ( $bannerData as $bannerIdentifier ) {
			if ( !$bannerIdentifier ) {
				throw new \Exception( 'A configured banner has an empty name.' );
			}
			$banners[] = new Banner( $bannerIdentifier );
		}
		return $banners;
	}

	private function parseConfiguration(): array {
		try {
			return Yaml::parseFile( $this->configFile );
		}
		catch ( ParseException $exception ) {
			$this->logger->critical( 'Unable to read banner server config file.', [ 'exception' => $exception ] );
			return [];
		}
	}
}