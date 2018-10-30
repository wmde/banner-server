<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;

/**
 * @license GNU GPL v2+
 */
class SystemRandomIntegerGenerator implements RandomIntegerGenerator {

	public function getRandomInteger( int $min, int $max ): int {
		return random_int( $min, $max );
	}
}