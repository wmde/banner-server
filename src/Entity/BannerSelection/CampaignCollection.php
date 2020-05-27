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
	private array $campaigns;

	public function __construct( Campaign ...$campaigns ) {
		$this->campaigns = $campaigns;
	}

	public function getCampaign( \DateTime $dateTime ): ?Campaign {
		foreach ( $this->campaigns as $campaign ) {
			if ( $campaign->isInActiveDateRange( $dateTime ) ) {
				return $campaign;
			}
		}
		return null;
	}

	public function filter( callable $isValid ): CampaignCollection {
		return new CampaignCollection( ...array_filter( $this->campaigns, $isValid ) );
	}

	public function getFirstCampaign(): Campaign {
		if( $this->isEmpty() ) {
			throw new \OutOfBoundsException("No campaigns found.");
		}
		return $this->campaigns[0];
	}

	public function isEmpty(): bool {
		return count ( $this->campaigns ) === 0;
	}
}