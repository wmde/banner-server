<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\Entity\Visitor;
use WMDE\BannerServer\UseCase\BannerSelection\BannerSelectionUseCase;

/**
 * @license GPL-2.0-or-later
 */
class BannerSelectionController {

	public const IMPRESSION_COUNT_COOKIE = 'impCount';
	public const BUCKET_COOKIE = 'b';
	public const CATEGORY_COOKIE = 'cat';

	public function __construct(
		private readonly BannerSelectionUseCase $useCase,
		private readonly string $bannerPath
	) {
	}

	public function selectBanner( Request $request ): Response {
		$bannerResponseData = $this->useCase->selectBanner( $this->buildValuesFromRequest( $request ) );
		if ( !$bannerResponseData->displayBanner() ) {
			$response = new Response( '// Sorry, no banner for you!' );
			$response->headers->set( 'Content-Type', 'application/javascript; charset=UTF-8' );
			return $response;
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
		$rawCategories = (string)$request->cookies->get( self::CATEGORY_COOKIE, '' );
		$categories = array_filter( explode( ',', $rawCategories ) );
		return new Visitor(
			$request->cookies->getInt( self::IMPRESSION_COUNT_COOKIE, 0 ),
			(string)$request->cookies->get( self::BUCKET_COOKIE, null ) ?: null,
			$request->query->getInt( 'vWidth' ),
			...$categories
		);
	}

	private function getBannerUrl( string $bannerIdentifier ): string {
		return $this->bannerPath . $bannerIdentifier . '.js';
	}

	/**
	 * @param Visitor $visitor
	 * @param \DateTime $cookieExpirationDate
	 *
	 * @return Cookie[]
	 * @throws \Exception
	 */
	private function getCookies( Visitor $visitor, \DateTime $cookieExpirationDate ): array {
		return [
			Cookie::create( self::BUCKET_COOKIE, $visitor->getBucketIdentifier(), $cookieExpirationDate ),
			Cookie::create(
				self::IMPRESSION_COUNT_COOKIE,
				(string)$visitor->getTotalImpressionCount(),
				new \DateTime( 'midnight first day of january next year' )
			)
		];
	}
}
