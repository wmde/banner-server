<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class ActiveBannerSelectionData implements BannerSelectionData {

	private $bannerIdentifier;
	private $visitorData;
	private $campaignEnd;

	public function __construct( Visitor $visitorData, string $bannerIdentifier, \DateTime $campaignEnd ) {
		$this->visitorData = $visitorData;
		$this->bannerIdentifier = $bannerIdentifier;
		$this->campaignEnd = $campaignEnd;
	}

	public function displayBanner(): bool {
		return true;
	}

	public function getVisitorData(): Visitor {
		return $this->visitorData;
	}

	public function getBannerIdentifier(): string {
		return $this->bannerIdentifier;
	}

	public function getCampaignEnd(): \DateTime {
		return $this->campaignEnd;
	}
}