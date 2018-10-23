<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\UseCase\ProvideBannerValues;
use WMDE\BannerServer\UseCase\ProvideBannerUseCase;

/**
 * @license GNU GPL v2+
 */
class BannerController {

	public const IMPRESSION_COUNT_COOKIE = 'impCount';
	public const BANNER_IMPRESSION_COUNT_COOKIE = 'bImpCount';
	public const BUCKET_COOKIE = 'b';
	public const CAMPAIGN_COOKIE = 'cmp';

	private $useCase;

	public function __construct( ProvideBannerUseCase $useCase ) {
		$this->useCase = $useCase;
	}

	public function provideBanner( Request $request ): Response {
		$bannerResponseData = $this->useCase->provideBannerRequest( $this->buildValuesFromRequest( $request ) );
		if ( !$bannerResponseData->displayBanner() ) {
			return new Response( '', Response::HTTP_OK );
		}
		return new Response( 'Placeholder', Response::HTTP_OK );
	}

	private function buildValuesFromRequest( Request $request ): ProvideBannerValues {
		return new ProvideBannerValues(
			$request->cookies->getInt( self::IMPRESSION_COUNT_COOKIE, 0 ),
			$request->cookies->getInt( self::BANNER_IMPRESSION_COUNT_COOKIE, 0 ),
			$request->cookies->get( self::BUCKET_COOKIE, null ),
			$request->cookies->get( self::CAMPAIGN_COOKIE, null )
		);
	}
}