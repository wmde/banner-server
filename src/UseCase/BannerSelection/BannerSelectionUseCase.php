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
		if ( $visitor->hasDonated() ) {
			return $this->cancelBannerSelection( $visitor );
		}

		return new BannerSelection(
			'test',
			new Visitor( 1, 'Testbucket', false ),
			new \DateTime()
		);
	}

	private function cancelBannerSelection( Visitor $visitor ): BannerSelection {
		return new BannerSelection(
			null,
			$visitor,
			new \DateTime()
		);
	}
}