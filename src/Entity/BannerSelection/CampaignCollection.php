<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class CampaignCollection {

	/**
	 * @var Campaign[]
	 */
	private $campaigns;

	public function __construct( Campaign ...$campaigns ) {
		$this->campaigns = $campaigns;
	}

	public function getCampaigns(): array {
		return $this->campaigns;
	}
}