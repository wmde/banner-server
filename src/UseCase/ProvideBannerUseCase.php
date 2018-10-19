<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase;

/**
 * @license GNU GPL v2+
 */
class ProvideBannerUseCase {
	public function provideBannerRequest( ProvideBannerValues $request ): BannerResponse {
		return new BannerResponse( [ 'test' => 123 ], true );
	}
}