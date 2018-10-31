<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
interface RandomIntegerGenerator {

	public function getRandomInteger( int $min, int $max ): int;
}