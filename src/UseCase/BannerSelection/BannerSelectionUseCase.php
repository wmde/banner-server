<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\Utils\RandomIntegerInterface;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionUseCase {

	private $campaignCollection;
	private $currentCampaign;
	private $randomInteger;
	private $impressionThreshold;

	public function __construct( CampaignCollection $campaignCollection, RandomIntegerInterface $randomInteger, ImpressionThreshold $impressionThreshold ) {
		$this->campaignCollection = $campaignCollection;
		$this->randomInteger = $randomInteger;
		$this->impressionThreshold = $impressionThreshold;
	}

	public function provideBannerRequest( Visitor $visitor ): BannerSelection {
		if ( $visitor->hasDonated() ||
			$this->getCurrentCampaign() === null ||
			$this->impressionThreshold->isThresholdReached( $visitor->getTotalImpressionCount() ) ) {
			return BannerSelection::createEmptySelection( $visitor );
		}

		$visitorBucket = $this->getCurrentCampaign()->selectBucket(
			$visitor->getBucketIdentifier()
		);

		return BannerSelection::createBannerSelection(
			$visitorBucket->getBanner( $visitor->getTotalImpressionCount() ),
			new Visitor( 1, $visitorBucket->getIdentifier(), false ),
			$this->getCurrentCampaign()->getEnd()
		);
	}

	private function getCurrentCampaign(): ?Campaign {
		if ( $this->currentCampaign === null ) {
			$this->currentCampaign = $this->campaignCollection->getCampaign( new \DateTime() );
		}
		return $this->currentCampaign;
	}
}