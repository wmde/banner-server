<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase;

/**
 * @license GNU GPL v2+
 */
class ProvideBannerUseCase {
	public function provideBannerRequest( ProvideBannerValues $request ): BannerResponse {
		return new BannerResponse(
			new ProvideBannerValues( 1, 2, 'SomeCampaign', 'testAbc' ),
			true
		);
	}
}