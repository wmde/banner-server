<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

abstract class AbstractCookieControllerWithFixedLifetime extends AbstractCookieController {

	private readonly \DateInterval $cookieLifetime;

	public function __construct( string $cookieLifetime ) {
		$this->cookieLifetime = new \DateInterval( $cookieLifetime );
	}

	protected function getCookieLifetime(): \DateInterval {
		return $this->cookieLifetime;
	}
}
