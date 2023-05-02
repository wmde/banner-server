<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GPL-2.0-or-later
 */
class ImpressionThreshold {

	public function __construct(
		private readonly int $seasonalLimit
	) {
	}

	public function isThresholdReached( int $impressions ): bool {
		return $impressions >= $this->seasonalLimit;
	}
}
