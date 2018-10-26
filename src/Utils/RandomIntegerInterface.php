<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Utils;

/**
 * @license GNU GPL v2+
 */
interface RandomIntegerInterface {

	public function getRandomInteger( int $min, int $max ): int;
}