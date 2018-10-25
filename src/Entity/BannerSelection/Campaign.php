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

	public function getIdentifier(): string {
		return $this->identifier;
	}

	public function isInActiveDateRange( \DateTime $time ): bool {
		return $time->getTimestamp() > $this->start->getTimestamp() &&
			$time->getTimestamp() < $this->end->getTimestamp();
	}

	public function selectBucket( ?string $bucketId, callable $fallbackSelectionStrategy ): Bucket {
		foreach ( $this->buckets as $bucket ) {
			if ( $bucket->getIdentifier() === $bucketId ) {
				return $bucket;
			}
		}
		return $fallbackSelectionStrategy( $this->buckets );
	}

	public function impressionThresholdReached( int $visitorImpressions ): bool {
		return $this->impressionThreshold->getIsOverThreshold( $visitorImpressions );
	}
}