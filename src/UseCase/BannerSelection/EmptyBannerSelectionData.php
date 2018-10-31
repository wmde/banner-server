<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class EmptyBannerSelectionData implements BannerSelectionData {

	private $visitorData;

	public function __construct( Visitor $visitorData ) {
		$this->visitorData = $visitorData;
	}

	public function displayBanner(): bool {
		return false;
	}

	public function getVisitorData(): Visitor {
		return $this->visitorData;
	}

	public function getBannerIdentifier(): string {
		throw new \LogicException( 'Empty banner selection does not contain banner data.' );
	}

	public function getCampaignEnd(): \DateTime {
		throw new \LogicException( 'Empty banner selection does not contain campaign data.' );
	}
}