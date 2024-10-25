<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCookieController {
	public const CATEGORY_PARAM = 'c';

	public function index( Request $request ): Response {
		$categories = trim( (string)$request->query->get( self::CATEGORY_PARAM, '' ) );
		if ( $categories === '' || !preg_match( '/^[-0-9a-zA-Z_,]+$/', $categories ) ) {
			return $this->newHtmlResponse( 'No donation category specified', Response::HTTP_BAD_REQUEST );
		}
		if ( $this->isImageRequest( $request ) ) {
			$response = $this->newImageResponse();
		} else {
			$response = $this->newHtmlResponse( 'Thank you for donating' );
		}

		$expiry = ( new \DateTime() )->add( $this->getCookieLifetime() );
		$response->headers->setCookie( Cookie::create(
			BannerSelectionController::CATEGORY_COOKIE,
			$categories,
			$expiry,
			'/',
			null,
			true,
			true,
			false,
			Cookie::SAMESITE_NONE
		) );
		return $response;
	}

	abstract protected function getCookieLifetime(): \DateInterval;

	private function newHtmlResponse( string $message, int $status = Response::HTTP_OK ): Response {
		$html = "<!DOCTYPE html><html lang='en'><head><title>WMDE Banner Server</title>" .
			"<meta charset=utf-8></head><body>$message</body></html>";
		return new Response( $html, $status, [ 'content-type' => 'text/html' ] );
	}

	private function isImageRequest( Request $request ): bool {
		$contentTypes = $request->getAcceptableContentTypes();
		if ( count( $contentTypes ) < 1 ) {
			return false;
		}
		$preferredContent = $contentTypes[0];
		return strpos( $preferredContent, 'image/' ) === 0;
	}

	private function newImageResponse(): Response {
		return new Response( '', Response::HTTP_OK, [
			'content-type' => 'image/png',
		] );
	}
}
