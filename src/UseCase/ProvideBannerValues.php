<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase;

/**
 * @license GNU GPL v2+
 */
class ProvideBannerValues {

	private $impressionCount = 0;
	private $bannerImpressionCount = 0;
	private $bucketName;
	private $campaignName;

	public function __construct( int $impressionCount, int $bannerImpressionCount, string $campaignName, string $bucketName ) {
		$this->impressionCount = $impressionCount;
		$this->bannerImpressionCount = $bannerImpressionCount;
		$this->campaignName = $campaignName;
		$this->bucketName = $bucketName;
	}

	public function getImpressionCount(): int {
		return $this->impressionCount;
	}

	public function getBannerImpressionCount(): int {
		return $this->bannerImpressionCount;
	}

	public function getBucketName(): string {
		return $this->bucketName;
	}

	public function getCampaignName(): string {
		return $this->campaignName;
	}
}