<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\RequestStack;
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
	private $request;
	private $provideBannerValues;

	public function __construct( RequestStack $requestStack, ProvideBannerUseCase $useCase ) {
		$this->request = $requestStack->getCurrentRequest();
		$this->useCase = $useCase;
		$this->provideBannerValues = new ProvideBannerValues(
			$this->request->cookies->getInt( self::IMPRESSION_COUNT_COOKIE, 0 ),
			$this->request->cookies->getInt( self::BANNER_IMPRESSION_COUNT_COOKIE, 0 ),
			$this->request->cookies->get( self::BUCKET_COOKIE, null ),
			$this->request->cookies->get( self::CAMPAIGN_COOKIE, null )
		);
	}

	public function provideBanner(): Response {
		$bannerResponseData = $this->useCase->provideBannerRequest( $this->provideBannerValues );
		if ( !$bannerResponseData->displayBanner() ) {
			return new Response( '', Response::HTTP_OK );
		}
		return new Response( 'Placeholder', Response::HTTP_OK );
	}
}