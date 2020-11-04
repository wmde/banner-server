<?php

declare( strict_types = 1 );

namespace WMDE\BannerServer\Tests\Fixtures;

use Symfony\Component\HttpFoundation\Request;
use WMDE\BannerServer\Controller\BannerSelectionController;
use WMDE\BannerServer\Entity\Visitor;

class VisitorFixture {

	public const VISITOR_TEST_IMPRESSION_COUNT = 5;
	public const VISITOR_TEST_BUCKET = 'test_bucket';
	public const VISITOR_TEST_DONATION_CATEGORY = 'fundraising_2020';
	public const VISITOR_TEST_DISPLAY_WIDTH = 500;

	public static function getReturningVisitorRequest(): Request {
		return new Request(
			[ 'vWidth' => self::VISITOR_TEST_DISPLAY_WIDTH ],
			[],
			[],
			[
				BannerSelectionController::IMPRESSION_COUNT_COOKIE => self::VISITOR_TEST_IMPRESSION_COUNT,
				BannerSelectionController::BUCKET_COOKIE => self::VISITOR_TEST_BUCKET,
				BannerSelectionController::CATEGORY_COOKIE => self::VISITOR_TEST_DONATION_CATEGORY ]
		);
	}

	public static function getTestVisitor( ?int $displayWidth = null ): Visitor {
		return new Visitor(
			self::VISITOR_TEST_IMPRESSION_COUNT,
			self::VISITOR_TEST_BUCKET,
			$displayWidth ?? self::VISITOR_TEST_DISPLAY_WIDTH,
			self::VISITOR_TEST_DONATION_CATEGORY
		);
	}

	public static function getFirstTimeVisitor(): Visitor {
		return new Visitor(
			0,
			null,
			0
		);
	}
}
