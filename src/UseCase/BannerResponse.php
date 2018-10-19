<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase;

/**
 * @license GNU GPL v2+
 */
class BannerResponse {

	private $cookies = [];
	private $displayBanner;

	public function __construct( array $cookies, bool $displayBanner ) {
		$this->cookies = $cookies;
		$this->displayBanner = $displayBanner;
	}

	public function getCookies(): array {
		return $this->cookies;
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