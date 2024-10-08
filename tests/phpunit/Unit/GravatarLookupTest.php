<?php

namespace MediaWiki\Extension\Gravatar\Tests\Unit;

use MediaWiki\Extension\Gravatar\GravatarLookup;
use MediaWiki\Html\Html;
use MediaWiki\User\User;
use MediaWiki\User\UserFactory;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use MediaWikiUnitTestCase;
use Wikimedia\TestingAccessWrapper;
use function md5;

class GravatarLookupTest extends MediaWikiUnitTestCase {
	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getCurrentUserAvatar
	 */
	public function testGetCurrentUserAvatar(): void {
		$this->assertSame(
			Html::element(
				'div',
				[
					'id' => 'id',
					'class' => 'testclass ext-gravatar-avatar ext-gravatar-user-avatar'
				],
				'Content'
			),
			GravatarLookup::getCurrentUserAvatar(
				[ 'id' => 'id', 'class' => 'testclass' ],
				'Content'
			)
		);
	}

	private function getLookup( bool $optedIn, string $email ): GravatarLookup {
		$user = $this->createMock( User::class );
		$user->method( 'getEmail' )->willReturn( $email );

		$userFactory = $this->createMock( UserFactory::class );
		$userFactory->method( 'newFromUserIdentity' )->willReturn( $user );
		$optionsLookup = $this->createMock( UserOptionsLookup::class );
		$optionsLookup->method( 'getBoolOption' )->willReturn( $optedIn );

		return new GravatarLookup(
			$userFactory,
			$optionsLookup,
			'//test.com',
			'default',
			'g'
		);
	}

	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getImgAvatar
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getAvatarForUser
	 */
	public function testGetImgAvatar(): void {
		$lookup = $this->getLookup( true, '' );

		$this->assertSame(
			Html::element(
				'img',
				[
					'src' => '//test.com/avatar/?r=g&d=default',
					'alt' => 'avatar',
					'class' => 'ext-gravatar-avatar-image'
				]
			),
			$lookup->getImgAvatar( $this->createMock( UserIdentity::class ) )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getImgAvatar
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getAvatarForUser
	 */
	public function testGetImgAvatarWithAltAndClass(): void {
		$lookup = $this->getLookup( true, '' );

		$this->assertSame(
			Html::element(
				'img',
				[
					'src' => '//test.com/avatar/?r=g&d=default',
					'alt' => 'alt',
					'class' => 'testclass ext-gravatar-avatar-image'
				]
			),
			$lookup->getImgAvatar(
				$this->createMock( UserIdentity::class ),
				[
					'alt' => 'alt',
					'class' => [ 'testclass' ]
				]
			)
		);
	}

	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getAvatarForUser
	 *
	 * @dataProvider provideEmails
	 *
	 * @param string $email
	 */
	public function testGetAvatarForUser( string $email ): void {
		$lookup = $this->getLookup( true, $email );

		static::assertSame(
			'//test.com/avatar/' . md5( 'test@test.com' ) . '?r=g&d=default',
			$lookup->getAvatarForUser( $this->createMock( UserIdentity::class ) )
		);
	}

	/**
	 * Data provider for testGetAvatarForUser.
	 *
	 * @return string[][]
	 */
	public static function provideEmails(): array {
		return [
			'Uppercase email' => [ 'TEST@TEST.COM' ],
			'Email with trailing whitespace' => [ 'test@test.com     ' ],
			'Regular email' => [ 'test@test.com' ]
		];
	}

	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::getAvatarForUser
	 */
	public function testGetAvatarForUserWithSize(): void {
		$lookup = $this->getLookup( true, '' );

		static::assertSame(
			'//test.com/avatar/?r=g&d=default&s=32',
			$lookup->getAvatarForUser( $this->createMock( UserIdentity::class ), 32 )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\Gravatar\GravatarLookup::lookupEmailAddress
	 *
	 * @dataProvider provideEmailAndPreferences
	 *
	 * @param bool $optedIn
	 * @param bool $hasEmail
	 * @param string|null $expected
	 */
	public function testLookupEmailAddress(
		bool $optedIn,
		bool $hasEmail,
		?string $expected
	): void {
		/** @var GravatarLookup $lookup */
		$lookup = TestingAccessWrapper::newFromObject( $this->getLookup( $optedIn, $hasEmail ? 'test@test.com' : '' ) );

		static::assertSame(
			$expected,
			$lookup->lookupEmailAddress( $this->createMock( UserIdentity::class ) )
		);
	}

	/**
	 * Data provider for testLookupEmailAddress.
	 *
	 * @return array[]
	 */
	public static function provideEmailAndPreferences(): array {
		return [
			'Not opted-in, has email' => [ false, true, null ],
			'Opted-in, no email' => [ true, false, null ],
			'Opted-in, has email' => [ true, true, 'test@test.com' ]
		];
	}
}
