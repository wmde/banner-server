<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class Bucket {

	private $identifier;
	/**
	 * @var Banner[]
	 */
	private $banners = [];

	public function __construct( string $identifier, Banner $firstBanner, Banner ...$additionalBanners ) {
		$this->identifier = $identifier;
		$this->banners = array_merge( [$firstBanner], $additionalBanners );
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}

	private function getLastBanner(): Banner {
		return $this->banners[count( $this->banners ) - 1];
	}

	/**
	 * Decides which banner to return based on visitor's impression count
	 */
	public function getBanner( int $visitorImpressionCount ): string {
		if ( $visitorImpressionCount >= count( $this->banners ) ) {
			return $this->getLastBanner()->getIdentifier();
		}
		return $this->banners[$visitorImpressionCount]->getIdentifier();
	}
}