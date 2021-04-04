<?php

namespace MediaWiki\Extensions\Gravatar\Tests\Hook;

use Closure;
use ConfigFactory;
use HashConfig;
use MediaWiki\Extensions\Gravatar\Hook\Handler;
use MediaWiki\MediaWikiServices;
use MediaWikiUnitTestCase;
use PHPUnit\Framework\Constraint\IsInstanceOf;

class HandlerTest extends MediaWikiUnitTestCase {
	/**
	 * @covers \MediaWiki\Extensions\Gravatar\Hook\Handler::onMediaWikiServices
	 */
	public function testOnMediaWikiServicesMirageNotInstalled() : void {
		$services = $this->createMock( MediaWikiServices::class );
		$services->expects( static::never() )->method( 'addServiceManipulator' );
		$services->method( 'hasService' )->willReturn( false );

		( new Handler() )->onMediaWikiServices( $services );
	}

	/**
	 * @covers \MediaWiki\Extensions\Gravatar\Hook\Handler::onMediaWikiServices
	 */
	public function testOnMediaWikiServices() : void {
		$configFactory = new ConfigFactory();
		$configFactory->register( 'Gravatar', new HashConfig( [
			'GravatarIgnoredSkins' => []
		] ) );

		$services = $this->createMock( MediaWikiServices::class );
		$services->method( 'hasService' )->willReturn( true );
		$services->method( 'getConfigFactory' )->willReturn( $configFactory );
		$services->expects( static::once() )->method( 'addServiceManipulator' )->with(
			'MirageAvatarLookup',
			new IsInstanceOf( Closure::class )
		);

		( new Handler() )->onMediaWikiServices( $services );
	}
}
