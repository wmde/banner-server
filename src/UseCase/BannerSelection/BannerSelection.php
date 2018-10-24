<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class BannerSelection {

	private $bannerIdentifier;
	private $visitorData;
	private $campaignEnd;

	public function __construct( ?string $bannerIdentifier, Visitor $visitorData, \DateTime $campaignEnd ) {
		$this->bannerIdentifier = $bannerIdentifier;
		$this->visitorData = $visitorData;
		$this->campaignEnd = $campaignEnd;
	}

	public function displayBanner(): bool {
		return $this->bannerIdentifier !== null;
	}

	public function getBannerIdentifier(): ?string {
		return $this->bannerIdentifier;
	}

	public function getVisitorData(): Visitor {
		return $this->visitorData;
	}

	public function getCampaignEnd(): \DateTime {
		return $this->campaignEnd;
	}
}