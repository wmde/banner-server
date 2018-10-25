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

	public function __construct( string $identifier, Banner $mainBanner, Banner ...$otherBanners ) {
		$this->identifier = $identifier;
		$this->banners[] = $mainBanner;
		array_push( $this->banners, $otherBanners );
	}

	public function getIdentifier(): string {
		return $this->getIdentifier();
	}

	/**
	 * Returns banner for given impression count
	 * If impression count is over banner sequence, returns last banner in sequence
	 */
	public function getBanner( int $visitorImpressions ): string {
		if ( isset( $this->banners[$visitorImpressions] ) ) {
			return $this->banners[$visitorImpressions]->getIdentifier();
		}
		return $this->banners[count( $this->banners ) - 1]->getIdentifier();
	}
}