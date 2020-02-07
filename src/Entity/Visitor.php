<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity;

/**
 * @license GNU GPL v2+
 */
class Visitor {

	private $totalImpressionCount = 0;
	private $bucketIdentifier;
	private $activeCategories;

	/**
	 * @param int $totalImpressionCount How many banners the visitor has seen overall
	 * @param string|null $bucketIdentifier Last bucket identifier of the visitor (to put recurring visitors in the same buckets, but only per-campaign)
	 * @param string ...$activeCategories Categories the user has interacted with (close button, donation, etc)
	 */
	public function __construct( int $totalImpressionCount, ?string $bucketIdentifier, string ...$activeCategories ) {
		$this->totalImpressionCount = $totalImpressionCount;
		$this->bucketIdentifier = $bucketIdentifier;
		$this->activeCategories = $activeCategories;
	}

	public function getTotalImpressionCount(): int {
		return $this->totalImpressionCount;
	}

	public function getBucketIdentifier(): ?string {
		return $this->bucketIdentifier;
	}

	public function inCategory( string $categoryName ): bool {
		return in_array( $categoryName, $this->activeCategories, true );
	}

	/**
	 * @return string[]
	 */
	public function getCategories(): array {
		return $this->activeCategories;
	}
}