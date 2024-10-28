<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller allows to set the cookie lifetime dynamically with the "d" (="duration") query parameter.
 */
class AlreadyDonatedController extends AbstractCookieController {

	public const MAX_INTERVAL_LENGTH = 'P28D';

	private \DateInterval $cookieLifetime;

	public function __construct() {
		$this->cookieLifetime = new \DateInterval( self::MAX_INTERVAL_LENGTH );
	}

	public function index( Request $request ): Response {
		if ( $request->query->has( 'd' ) ) {
			$potentialLifetime = $this->getDurationHours( $request->query->get( 'd' ) );
			$now = new \DateTimeImmutable();
			if ( $now->add( $potentialLifetime ) < $now->add( new \DateInterval( self::MAX_INTERVAL_LENGTH ) ) ) {
				$this->cookieLifetime = $potentialLifetime;
			}
		}
		return parent::index( $request );
	}

	protected function getCookieLifetime(): \DateInterval {
		return $this->cookieLifetime;
	}

	private function getDurationHours( float|bool|int|string|null $durationInHours ): \DateInterval {
		$durationInHours = filter_var( $durationInHours, FILTER_VALIDATE_INT );
		if ( $durationInHours === false ) {
			return new \DateInterval( self::MAX_INTERVAL_LENGTH );
		}
		return new \DateInterval( 'PT' . $durationInHours . 'H' );
	}
}
