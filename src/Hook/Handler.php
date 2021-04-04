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

class Handler implements BeforePageDisplayHook, GetPreferencesHook {
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
}
