<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;
use WMDE\BannerServer\UseCase\BannerSelection\Visitor;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionController {

	public const IMPRESSION_COUNT_COOKIE = 'impCount';
	public const BANNER_IMPRESSION_COUNT_COOKIE = 'bImpCount';
	public const BUCKET_COOKIE = 'b';
	public const CAMPAIGN_COOKIE = 'cmp';
	public const DONATED_COOKIE = 'd';

	private $useCase;

	public function __construct( BannerSelectionUseCase $useCase ) {
		$this->useCase = $useCase;
	}

	public function selectBanner( Request $request ): Response {
		$bannerResponseData = $this->useCase->provideBannerRequest( $this->buildValuesFromRequest( $request ) );
		if ( !$bannerResponseData->displayBanner() ) {
			return new Response( '', Response::HTTP_OK );
		}
		return new Response( 'Placeholder', Response::HTTP_OK );
	}

	private function buildValuesFromRequest( Request $request ): Visitor {
		return new Visitor(
			$request->cookies->getInt( self::IMPRESSION_COUNT_COOKIE, 0 ),
			$request->cookies->get( self::BUCKET_COOKIE, null ),
			$request->cookies->get( self::DONATED_COOKIE, null )
		);
	}
}