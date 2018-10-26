<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Utils;

use WMDE\BannerServer\Utils\RandomIntegerInterface;

/**
 * @license GNU GPL v2+
 */
class FakeRandomInteger implements RandomIntegerInterface {

	private $returnValue;

	public function __construct( int $returnValue ) {
		$this->returnValue = $returnValue;
	}

	public function getRandomInteger( int $min, int $max ): int {
		return $this->returnValue;
	}
}