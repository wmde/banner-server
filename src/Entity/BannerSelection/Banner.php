<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GPL-2.0-or-later
 */
class Banner {

	public function __construct(
		private readonly string $identifier
	) {
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}
}
