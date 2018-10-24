<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionUseCase {

	private $campaignCollection;

	public function __construct( CampaignCollection $campaignCollection ) {
		$this->campaignCollection = $campaignCollection;
	}

	public function provideBannerRequest( Visitor $visitor ): BannerSelection {
		return new BannerSelection(
			new Visitor( 1, 'Testbucket', false ),
			true
		);
	}
}