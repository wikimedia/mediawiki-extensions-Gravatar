<?php

namespace MediaWiki\Extension\Gravatar\Tests\Structure;

use MediaWiki\Extension\Gravatar\GravatarLookup;
use MediaWiki\Tests\Structure\BundleSizeTestBase;

/**
 * @coversNothing
 */
class BundleSizeTest extends BundleSizeTestBase {
	/**
	 * @before
	 *
	 * Mock GravatarLookup to prevent it from doing lookups.
	 */
	protected function replaceGravatarLookupWithMockSetUp(): void {
		$this->setService( 'GravatarLookup', $this->createMock( GravatarLookup::class ) );
	}

	/** @inheritDoc */
	public static function getBundleSizeConfigData(): string {
		return __DIR__ . '/../../../bundlesize.config.json';
	}
}
