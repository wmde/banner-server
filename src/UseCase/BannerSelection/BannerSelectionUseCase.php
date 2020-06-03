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
		$remainingCampaigns = $this->campaignCollection->filter(
			function ( Campaign $campaign ) use ( $visitor ): bool {
				return (
					$campaign->isInActiveDateRange( new \DateTime() ) &&
					$campaign->isInDisplayRange( $visitor->getDisplayWidth() ) &&
					!$this->impressionThreshold->isThresholdReached( $visitor->getTotalImpressionCount() ) &&
					!$visitor->inCategory( $campaign->getCategory() ) &&
					!$this->isRatioLimited( $campaign->getDisplayPercentage() )
				);
			}
		);

		if ( $remainingCampaigns->isEmpty() ) {
			return new EmptyBannerSelectionData( $visitor );
		}

		$selectedCampaign = $remainingCampaigns->getFirstCampaign();

		$visitorBucket = $selectedCampaign->selectBucket(
			$visitor->getBucketIdentifier()
		);

		return new ActiveBannerSelectionData(
			new Visitor(
				$visitor->getTotalImpressionCount() + 1,
				$visitorBucket->getIdentifier(),
				$visitor->getDisplayWidth(),
				...$visitor->getCategories()
			),
			$visitorBucket->getBanner( $visitor->getTotalImpressionCount() ),
			$selectedCampaign->getEnd()
		);
	}

	private function isRatioLimited( int $campaignDisplayPercent ): bool {
		return $this->rng->getRandomInteger( 1, 100 ) > $campaignDisplayPercent;
	}
}