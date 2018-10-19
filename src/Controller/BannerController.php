<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WMDE\BannerServer\UseCase\BannerProvideRequest;
use WMDE\BannerServer\UseCase\BannerProvideUseCase;

/**
 * @license GNU GPL v2+
 */
class BannerController {

	public function provide(): Response {
		$useCase = new BannerProvideUseCase();
		return $useCase->processBannerRequest( $this->buildRequest() );
	}

	private function buildRequest(): BannerProvideRequest {
		$request = Request::createFromGlobals();
		return new BannerProvideRequest(
			$request->cookies->getInt( 'impCount', 0 ),
			$request->cookies->getInt( 'bImpCount', 0 ),
			$request->cookies->get( 'b', null ),
			$request->cookies->get( 'cmp', null )
		);
	}
}