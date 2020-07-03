<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GPL-2.0-or-later
 */
interface RandomIntegerGenerator {

	public function getRandomInteger( int $min, int $max ): int;
}
