<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;
use WMDE\BannerServer\Entity\Visitor;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionUseCase {

	private $campaignCollection;
	private $impressionThreshold;
	private $rng;

	/**
	 * @var Campaign
	 */
	private $currentCampaign;

	public function __construct( CampaignCollection $campaignCollection, ImpressionThreshold $impressionThreshold, RandomIntegerGenerator $rng ) {
		$this->campaignCollection = $campaignCollection;
		$this->impressionThreshold = $impressionThreshold;
		$this->rng = $rng;
	}

	public function selectBanner( Visitor $visitor ): BannerSelectionData {
		if ( $visitor->hasDonated() ||
			$this->getCurrentCampaign() === null ||
			$this->impressionThreshold->isThresholdReached( $visitor->getTotalImpressionCount() ) ||
			$this->isRatioLimited( $this->getCurrentCampaign()->getDisplayPercentage() ) ) {
			return new EmptyBannerSelectionData( $visitor );
		}

		$visitorBucket = $this->getCurrentCampaign()->selectBucket(
			$visitor->getBucketIdentifier()
		);

		return new ActiveBannerSelectionData(
			new Visitor( $visitor->getTotalImpressionCount() + 1, $visitorBucket->getIdentifier(), false ),
			$visitorBucket->getBanner( $visitor->getTotalImpressionCount() ),
			$this->getCurrentCampaign()->getEnd()
		);
	}

	private function getCurrentCampaign(): ?Campaign {
		if ( $this->currentCampaign === null ) {
			$this->currentCampaign = $this->campaignCollection->getCampaign( new \DateTime() );
		}
		return $this->currentCampaign;
	}

	private function isRatioLimited( int $campaignDisplayPercent ): bool {
		return $this->rng->getRandomInteger( 1, 100 ) > $campaignDisplayPercent;
	}
}