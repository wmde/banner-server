<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultRouteController {
	public function index(): Response {
		return new Response(
			'Server is up and reachable',
			Response::HTTP_OK,
			[ 'Content-Type' => 'text/plain' ]
		);
	}
}
