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

	public static function createBannerSelection( string $bannerIdentifier, Visitor $visitorData, \DateTime $campaignEnd ): self {
		return new self( $bannerIdentifier, $visitorData, $campaignEnd );
	}

	public static function createEmptySelection( Visitor $visitor ): self {
		return new self( null, $visitor, new \DateTime() );
	}

	private function __construct( ?string $bannerIdentifier, Visitor $visitorData, \DateTime $campaignEnd ) {
		$this->bannerIdentifier = $bannerIdentifier;
		$this->visitorData = $visitorData;
		$this->campaignEnd = $campaignEnd;
	}

	public function displayBanner(): bool {
		return $this->bannerIdentifier !== null;
	}

	public function getBannerIdentifier(): string {
		assert( is_string( $this->bannerIdentifier ) );
		return $this->bannerIdentifier;
	}

	public function getVisitorData(): Visitor {
		return $this->visitorData;
	}

	public function getCampaignEnd(): \DateTime {
		return $this->campaignEnd;
	}
}