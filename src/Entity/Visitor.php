<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity;

/**
 * @license GNU GPL v2+
 */
class Visitor {

	private int $totalImpressionCount = 0;
	private ?string $bucketIdentifier;
	private array $activeCategories;
	private int $displayWidth;

	/**
	 * @param int $totalImpressionCount How many banners the visitor has seen overall
	 * @param string|null $bucketIdentifier Last bucket identifier of the visitor (to put recurring visitors in the same buckets, but only per-campaign)
	 * @param int $displayWidth Viewport width of the visitor's browser window.
	 * @param string ...$activeCategories
	 */
	public function __construct(
			int $totalImpressionCount,
			?string $bucketIdentifier,
			int $displayWidth,
			string ...$activeCategories ) {
		$this->totalImpressionCount = $totalImpressionCount;
		$this->bucketIdentifier = $bucketIdentifier;
		$this->displayWidth = $displayWidth;
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

	public function getDisplayWidth(): ?int {
		return $this->displayWidth;
	}

	/**
	 * @return string[]
	 */
	public function getCategories(): array {
		return $this->activeCategories;
	}
}