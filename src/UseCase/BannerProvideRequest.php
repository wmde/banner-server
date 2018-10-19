<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase;

/**
 * @license GNU GPL v2+
 */
class BannerProvideRequest {

	private $impressionCount;
	private $bannerImpressionCount;
	private $bucketName;
	private $campaignName;

	public function __construct( int $impressionCount, int $bannerImpressionCount, string $bucketName, string $campaignName ) {
		$this->impressionCount = $impressionCount;
		$this->bannerImpressionCount = $bannerImpressionCount;
		$this->bucketName = $bucketName;
		$this->campaignName = $campaignName;
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