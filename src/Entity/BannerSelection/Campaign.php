<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Entity\BannerSelection;

/**
 * @license GNU GPL v2+
 */
class Campaign {

	private string $identifier;
	private \DateTime $start;
	private \DateTime $end;
	private string $category;
	private RandomIntegerGenerator $rng;
	private int $displayPercentage;
	private ?int $minDisplayWidth;
	private ?int $maxDisplayWidth;

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
		?int $minDisplayWidth = null,
		?int $maxDisplayWidth = null,
		Bucket $firstBucket,
		Bucket ...$additionalBuckets ) {

		$this->identifier = $identifier;
		$this->start = $start;
		$this->end = $end;
		$this->category = $category;
		$this->displayPercentage = $displayPercentage;
		$this->rng = $rng;
		$this->minDisplayWidth = $minDisplayWidth;
		$this->maxDisplayWidth = $maxDisplayWidth;
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

	public function isInDisplayRange( int $width ): bool {

		if ( $this->minDisplayWidth !== null && $width < $this->minDisplayWidth ) {
			return false;
		}
		if( $this->maxDisplayWidth !== null && $width > $this->maxDisplayWidth ){
			return false;
		}
		return true;
	}
}