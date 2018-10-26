<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

use WMDE\BannerServer\Utils\RandomIntegerInterface;

/**
 * @license GNU GPL v2+
 */
class Campaign {

	private $identifier;
	private $start;
	private $end;
	private $rng;
	private $displayRatio;

	/**
	 * @var Bucket[]
	 */
	private $buckets;

	public function __construct(
		string $identifier,
		\DateTime $start,
		\DateTime $end,
		float $displayRatio,
		RandomIntegerInterface $rng,
		Bucket $mainBucket,
		Bucket ...$additionalBuckets ) {

		$this->identifier = $identifier;
		$this->start = $start;
		$this->end = $end;
		$this->displayRatio = $displayRatio;
		$this->rng = $rng;
		$this->buckets = $additionalBuckets;
		array_unshift( $this->buckets, $mainBucket );
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}

	public function getEnd(): \DateTime {
		return $this->end;
	}

	public function isInActiveDateRange( \DateTime $time ): bool {
		return $time->getTimestamp() >= $this->start->getTimestamp() &&
			$time->getTimestamp() <= $this->end->getTimestamp();
	}

	public function selectBucket( ?string $bucketId ): Bucket {
		foreach ( $this->buckets as $bucket ) {
			if ( $bucket->getIdentifier() === $bucketId ) {
				return $bucket;
			}
		}
		return $this->buckets[$this->rng->getRandomInteger( 0, count( $this->buckets ) - 1 )];
	}

	public function isRatioLimited(): bool {
		return ( $this->rng->getRandomInteger( 1, 100 ) / 100 ) > $this->displayRatio;
	}
}