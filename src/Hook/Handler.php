<?php

namespace MediaWiki\Extensions\Gravatar\Hook;

use MediaWiki\Extensions\Gravatar\GravatarLookup;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\MediaWikiServicesHook;
use MediaWiki\MediaWikiServices;
use MediaWiki\Preferences\Hook\GetPreferencesHook;
use MediaWiki\Skins\Mirage\Avatars\AvatarLookup;
use MediaWiki\User\UserIdentity;
use OutputPage;
use Skin;
use User;
use function in_array;

class Handler implements BeforePageDisplayHook, GetPreferencesHook, MediaWikiServicesHook {
	/**
	 * @inheritDoc
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ) : void {
		$out->addModuleStyles( 'ext.Gravatar.avatar.styles' );
	}

	/**
	 * @inheritDoc
	 *
	 * @param User $user
	 * @param array &$preferences
	 */
	public function onGetPreferences( $user, &$preferences ) : void {
		$preferences['gravatar-avatar'] = [
			'type' => 'info',
			'raw' => true,
			'label-message' => 'prefs-gravatar-avatar-label',
			'section' => 'personal/info/gravatar',
			'default' => GravatarLookup::getCurrentUserAvatar()
		];
		$preferences['gravatar-use-gravatar'] = [
			'type' => 'check',
			'label-message' => 'prefs-gravatar-use-gravatar-label',
			'help-message' => [ 'prefs-gravatar-use-gravatar-help', 'https://automattic.com/privacy/' ],
			'section' => 'personal/info/gravatar'
		];
	}

	/**
	 * @inheritDoc
	 *
	 * @param MediaWikiServices $services
	 */
	public function onMediaWikiServices( $services ) : void {
		if ( !$services->hasService( 'MirageAvatarLookup' ) ) {
			return;
		}

		$services->addServiceManipulator(
			'MirageAvatarLookup',
			static function ( $_, MediaWikiServices $services ) {
				$ignoredSkins = $services->getConfigFactory()->makeConfig( 'Gravatar' )
					->get( 'GravatarIgnoredSkins' );

				if ( in_array( 'mirage', $ignoredSkins, true ) ) {
					return null;
				}

				return new class( $services->getService( 'GravatarLookup' ) ) extends AvatarLookup {
					/** @var GravatarLookup */
					private $lookup;

					/**
					 * @param GravatarLookup $lookup
					 */
					public function __construct( GravatarLookup $lookup ) {
						$this->lookup = $lookup;
					}

					/**
					 * @inheritDoc
					 */
					public function getAvatarForUser( UserIdentity $user ) : string {
						return $this->lookup->getAvatarForUser( $user );
					}
				};
			}
		);
	}
}
