<?php

namespace MediaWiki\Extensions\Gravatar\Test\Integration\ResourceLoader;

use HashConfig;
use MediaWiki\Extensions\Gravatar\GravatarLookup;
use MediaWiki\Extensions\Gravatar\ResourceLoader\GravatarResourceLoaderModule;
use MediaWikiIntegrationTestCase;
use ResourceLoaderContext;

class GravatarResourceLoaderModuleTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \MediaWiki\Extensions\Gravatar\ResourceLoader\GravatarResourceLoaderModule::getLessVars
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

	/**
	 * @covers \MediaWiki\Extensions\Gravatar\ResourceLoader\GravatarResourceLoaderModule::getDefinitionSummary
	 */
	public function testGetDefinitionSummary(): void {
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

		list( , , $summary ) = $module->getDefinitionSummary( $this->createMock(
			ResourceLoaderContext::class ) );

		static::assertArrayHasKey( 'AvatarUrl', $summary );
		static::assertSame( '//avatarprovider.com/avatarpath', $summary['AvatarUrl'] );
	}
}
