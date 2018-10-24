<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
interface ImpressionThresholdInterface {
	public function getIsOverThreshold( int $visitorTotalImpressions );
}