<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class Banner {

	private $identifier;

	public function __construct( string $identifier ) {
		$this->identifier = $identifier;
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}
}