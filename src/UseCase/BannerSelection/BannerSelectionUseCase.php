<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThresholdInterface;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionUseCase {

	private $campaignCollection;
	private $currentCampaign;
	private $impressionThreshold;

	public function __construct( CampaignCollection $campaignCollection, ImpressionThresholdInterface $impressionThreshold ) {
		$this->campaignCollection = $campaignCollection;
		$this->impressionThreshold = $impressionThreshold;
	}

	public function provideBannerRequest( Visitor $visitor ): BannerSelection {
		if ( $visitor->hasDonated() ||
			$this->getCurrentCampaign() === null ||
			$this->impressionThreshold->getIsOverThreshold( $visitor->getTotalImpressionCount() ) ) {
			return BannerSelection::createEmptySelection( $visitor );
		}

		$visitorBucket = $this->getCurrentCampaign()->selectBucket(
			$visitor->getBucketIdentifier(),
			[ self::class, 'selectRandomBucket' ]
		);

		return BannerSelection::createBannerSelection(
			$visitorBucket->getBanner( $visitor->getTotalImpressionCount() ),
			new Visitor( 1, $visitorBucket->getIdentifier(), false ),
			new \DateTime()
		);
	}

	private function getCurrentCampaign(): ?Campaign {
		if ( $this->currentCampaign === null ) {
			$this->currentCampaign = $this->campaignCollection->getCampaign( new \DateTime() );
		}
		return $this->currentCampaign;
	}

	public static function selectRandomBucket( Bucket ...$buckets ): Bucket {
		return $buckets[random_int( 0, count( $buckets ) )];
	}
}