<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Utils;

use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;

/**
 * @license GPL-2.0-or-later
 */
class FakeRandomIntegerGenerator implements RandomIntegerGenerator {

	public function __construct(
		private readonly int $returnValue
	) {
	}

	public function getRandomInteger( int $min, int $max ): int {
		return $this->returnValue;
	}
}
