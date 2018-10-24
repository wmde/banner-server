<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class BannerSelection {

	private $bannerValues = [];
	private $displayBanner;

	public function __construct( Visitor $bannerValues, bool $displayBanner ) {
		$this->displayBanner = $displayBanner;
		$this->bannerValues = $bannerValues;
	}

	public function getBannerValues(): array {
		return $this->bannerValues;
	}

	public function displayBanner(): bool {
		return $this->displayBanner;
	}

	public function getHeaders(): array {
		return [
			'Content-Type' => 'application/javascript'
		];
	}
}