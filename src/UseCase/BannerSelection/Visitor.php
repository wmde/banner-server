<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class Visitor {

	private $totalImpressionCount = 0;
	private $bucketIdentifier;
	private $hasDonated;

	public function __construct( int $totalImpressionCount, string $bucketIdentifier, bool $hasDonated ) {
		$this->totalImpressionCount = $totalImpressionCount;
		$this->bucketIdentifier = $bucketIdentifier;
		$this->hasDonated = $hasDonated;
	}

	public function getTotalImpressionCount(): int {
		return $this->totalImpressionCount;
	}

	public function getBucketIdentifier(): string {
		return $this->bucketIdentifier;
	}
}