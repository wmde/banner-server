<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\UseCase\BannerSelection;

use WMDE\BannerServer\Entity\Visitor;

/**
 * @license GNU GPL v2+
 */
interface BannerSelectionData {
	public function displayBanner(): bool;
	public function getBannerIdentifier(): string;
	public function getVisitorData(): Visitor;
	public function getCampaignEnd(): \DateTime;
}