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
	private $category;
	private $rng;
	private $displayPercentage;

	/**
	 * @var Bucket[]
	 */
	private $buckets;

	public function __construct(
		string $identifier,
		\DateTime $start,
		\DateTime $end,
		int $displayPercentage,
		string $category,
		RandomIntegerGenerator $rng,
		Bucket $firstBucket,
		Bucket ...$additionalBuckets ) {

		$this->identifier = $identifier;
		$this->start = $start;
		$this->end = $end;
		$this->category = $category;
		$this->displayPercentage = $displayPercentage;
		$this->rng = $rng;
		$this->buckets = array_merge( [$firstBucket], $additionalBuckets );
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}

	public function getEnd(): \DateTime {
		return $this->end;
	}

	public function getCategory(): string {
		return $this->category;
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

	public function getDisplayPercentage(): int {
		return $this->displayPercentage;
	}
}