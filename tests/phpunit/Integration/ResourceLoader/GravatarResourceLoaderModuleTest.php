<?php

namespace MediaWiki\Extension\Gravatar\Test\Integration\ResourceLoader;

use MediaWiki\Config\HashConfig;
use MediaWiki\Extension\Gravatar\GravatarLookup;
use MediaWiki\Extension\Gravatar\ResourceLoader\GravatarResourceLoaderModule;
use MediaWiki\ResourceLoader\Context as ResourceLoaderContext;
use MediaWikiIntegrationTestCase;

class GravatarResourceLoaderModuleTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \MediaWiki\Extension\Gravatar\ResourceLoader\GravatarResourceLoaderModule::getLessVars
	 */
	public function testGetLessVars(): void {
		$lookup = $this->createMock( GravatarLookup::class );
		$lookup->method( 'getAvatarForUser' )->willReturn(
			'//avatarprovider.com/avatarpath'
		);

		$module = new GravatarResourceLoaderModule(
			[],
			'',
			'',
			$lookup,
			new HashConfig( [
				'GravatarIgnoredSkins' => []
			] )
		);

		static::assertSame(
			[ 'gravatar-avatar-url' => 'url(//avatarprovider.com/avatarpath)' ],
			$module->getLessVars( $this->createMock( ResourceLoaderContext::class ) )
		);
	}
}
