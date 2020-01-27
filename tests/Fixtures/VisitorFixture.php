<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Fixtures;

use Symfony\Component\HttpFoundation\Request;
use WMDE\BannerServer\Controller\BannerSelectionController;
use WMDE\BannerServer\Entity\Visitor;

class VisitorFixture {

	public const VISITOR_TEST_IMPRESSION_COUNT = 5;
	public const VISITOR_TEST_BUCKET = 'test_bucket';
	public const VISITOR_TEST_DONATION_HISTORY = false;

	public static function getReturningVisitorRequest(): Request {
		return new Request(
			[],
			[],
			[],
			[
				BannerSelectionController::IMPRESSION_COUNT_COOKIE => self::VISITOR_TEST_IMPRESSION_COUNT,
				BannerSelectionController::BUCKET_COOKIE => self::VISITOR_TEST_BUCKET,
				BannerSelectionController::DONATED_COOKIE => self::VISITOR_TEST_DONATION_HISTORY ]
		);
	}

	public static function getTestVisitor(): Visitor {
		return new Visitor(
			self::VISITOR_TEST_IMPRESSION_COUNT,
			self::VISITOR_TEST_BUCKET,
			self::VISITOR_TEST_DONATION_HISTORY
		);
	}

	public static function getFirstTimeVisitor(): Visitor {
		return new Visitor(
			0,
			null,
			false
		);
	}
}
