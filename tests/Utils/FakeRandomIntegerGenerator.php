<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Utils;

use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;

/**
 * @license GNU GPL v2+
 */
class FakeRandomIntegerGenerator implements RandomIntegerGenerator {

	private $returnValue;

	public function __construct( int $returnValue ) {
		$this->returnValue = $returnValue;
	}

	public function getRandomInteger( int $min, int $max ): int {
		return $this->returnValue;
	}
}