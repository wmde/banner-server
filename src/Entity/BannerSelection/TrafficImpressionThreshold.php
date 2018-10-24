<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class TrafficImpressionThreshold implements ImpressionThresholdInterface {

	private $trafficLimit;
	private $seasonalLimit;

	public function __construct( float $trafficLimit, int $seasonalLimit ) {
		$this->trafficLimit = $trafficLimit;
		$this->seasonalLimit = $seasonalLimit;
	}

	public function getIsOverThreshold( int $visitorTotalImpressions ): bool {
		return $this->isTrafficLimited() || $this->isImpressionLimitReached( $visitorTotalImpressions );
	}

	private function isTrafficLimited(): bool {
		return ( random_int( 0, 100 ) / 100 ) <= $this->trafficLimit;
	}

	private function isImpressionLimitReached( int $impressions ): bool {
		return $impressions >= $this->seasonalLimit;
	}
}