<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Bucket;
use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;

/**
 * @license GNU GPL v2+
 */
class BannerSelectionUseCase {

	private $campaignCollection;
	private $currentCampaign;

	public function __construct( CampaignCollection $campaignCollection ) {
		$this->campaignCollection = $campaignCollection;
	}

	public function provideBannerRequest( Visitor $visitor ): BannerSelection {
		if ( $visitor->hasDonated() ||
			$this->getCurrentCampaign() === null ||
			$this->getCurrentCampaign()->impressionThresholdReached( $visitor->getTotalImpressionCount() ) ) {
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