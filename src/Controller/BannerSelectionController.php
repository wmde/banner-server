<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelection;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;
use WMDE\BannerServer\UseCase\BannerSelection\Visitor;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionController {

	public const IMPRESSION_COUNT_COOKIE = 'impCount';
	public const BUCKET_COOKIE = 'b';
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

		$response = new RedirectResponse(
			$this->getBannerUrl( $bannerResponseData->getBannerIdentifier() ),
			Response::HTTP_FOUND
		);

		foreach ( $this->getCookies(
			$bannerResponseData->getVisitorData(),
			$bannerResponseData->getCampaignEnd()->modify( '+2 week' )
		) as $cookie ) {
			$response->headers->setCookie( $cookie );
		}

		return $response;
	}

	private function buildValuesFromRequest( Request $request ): Visitor {
		return new Visitor(
			$request->cookies->getInt( self::IMPRESSION_COUNT_COOKIE, 0 ),
			$request->cookies->get( self::BUCKET_COOKIE, null ),
			$request->cookies->get( self::DONATED_COOKIE, false )
		);
	}

	private function getBannerUrl( string $bannerIdentifier ): string {
		return 'content/' . $bannerIdentifier . '.js';
	}

	private function getCookies( Visitor $visitor, \DateTime $cookieExpirationDate ): array {
		return [
			new Cookie( self::BUCKET_COOKIE, $visitor->getBucketIdentifier(), $cookieExpirationDate ),
			new Cookie(
				self::IMPRESSION_COUNT_COOKIE,
				(string)$visitor->getTotalImpressionCount(),
				$cookieExpirationDate
			)
		];
	}
}