<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\BannerSelection\Campaign;
use WMDE\BannerServer\Entity\BannerSelection\CampaignCollection;
use WMDE\BannerServer\Entity\BannerSelection\ImpressionThreshold;
use WMDE\BannerServer\Entity\BannerSelection\RandomIntegerGenerator;
use WMDE\BannerServer\Entity\Visitor;

class BannerSelectionUseCase {

	public function __construct(
		private readonly CampaignCollection $campaignCollection,
		private readonly ImpressionThreshold $impressionThreshold,
		private readonly RandomIntegerGenerator $rng
	) {
	}

	public function selectBanner( Visitor $visitor ): BannerSelectionData {
		$remainingCampaigns = $this->campaignCollection->filter(
			function ( Campaign $campaign ) use ( $visitor ): bool {
				$displayWidth = $visitor->getDisplayWidth() ?? 0;

				return (
					$campaign->isInActiveDateRange( new \DateTime() ) &&
					$campaign->isInDisplayRange( $displayWidth ) &&
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

		$displayWidth = $visitor->getDisplayWidth() ?? 0;

		return new ActiveBannerSelectionData(
			new Visitor(
				$visitor->getTotalImpressionCount() + 1,
				$visitorBucket->getIdentifier(),
				$displayWidth,
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
