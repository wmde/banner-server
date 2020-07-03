<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;

/**
 * @license GPL-2.0-or-later
 */
class SystemRandomIntegerGenerator implements RandomIntegerGenerator {

	public function getRandomInteger( int $min, int $max ): int {
		return random_int( $min, $max );
	}
}
