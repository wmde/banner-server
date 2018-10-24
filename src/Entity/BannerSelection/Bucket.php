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
}