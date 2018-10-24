<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class Campaign {

	private $identifier;
	private $start;
	private $end;
	private $impressionThreshold;

	/**
	 * @var Bucket[]
	 */
	private $buckets;

	public function __construct( string $identifier, \DateTime $start, \DateTime $end, array $buckets, ImpressionThresholdInterface $impressionThreshold ) {
		$this->identifier = $identifier;
		$this->start = $start;
		$this->end = $end;
		$this->buckets = $buckets;
		$this->impressionThreshold = $impressionThreshold;
	}
}