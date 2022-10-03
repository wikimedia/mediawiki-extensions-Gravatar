<?php

namespace MediaWiki\Extension\Gravatar;

use Html;
use MediaWiki\User\UserFactory;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use function md5;
use function strtolower;
use function trim;
use function wfAppendQuery;

class GravatarLookup {
	private UserFactory $userFactory;

	private UserOptionsLookup $userOptionsLookup;

	private string $gravatarServer;

	private string $defaultAvatar;

	private string $acceptedAvatarRating;

	/**
	 * @codeCoverageIgnore
	 *
	 * @param UserFactory $userFactory
	 * @param UserOptionsLookup $userOptionsLookup
	 * @param string $gravatarServer
	 * @param string $defaultAvatar Default avatar to display; either an url, or a supported
	 * default avatar name
	 * @param string $acceptedAvatarRating Accepted rating of avatars
	 */
	public function __construct(
		UserFactory $userFactory,
		UserOptionsLookup $userOptionsLookup,
		string $gravatarServer,
		string $defaultAvatar,
		string $acceptedAvatarRating
	) {
		$this->userFactory = $userFactory;
		$this->userOptionsLookup = $userOptionsLookup;
		$this->gravatarServer = $gravatarServer[-1] === '/' ? $gravatarServer : ( $gravatarServer . '/' );
		$this->defaultAvatar = $defaultAvatar;
		$this->acceptedAvatarRating = $acceptedAvatarRating;
	}

	/**
	 * Create a <div> that contains the avatar of the user viewing the page.
	 * The avatar is provided through CSS and will differ based on the user viewing the page.
	 *
	 * @param array $parameters
	 * @param string $content
	 * @return string
	 */
	public static function getCurrentUserAvatar(
		array $parameters = [],
		string $content = ''
	): string {
		$parameters['class'] = (array)( $parameters['class'] ?? [] );
		$parameters['class'][] = 'ext-gravatar-avatar';
		$parameters['class'][] = 'ext-gravatar-user-avatar';

		return Html::element(
			'div',
			$parameters,
			$content
		);
	}

	/**
	 * Create a <img> displaying the avatar of the given user.
	 *
	 * @param UserIdentity $userIdentity
	 * @param array $extraParameters
	 * @param int $size Size of the avatar
	 * @return string
	 */
	public function getImgAvatar(
		UserIdentity $userIdentity,
		array $extraParameters = [],
		int $size = 0
	): string {
		$class = (array)( $extraParameters['class'] ?? [] );
		$class[] = 'ext-gravatar-avatar-image';

		return Html::element(
			'img',
			[
				'src' => $this->getAvatarForUser( $userIdentity, $size ),
				'alt' => $extraParameters['alt'] ?? 'avatar',
				'class' => $class
			] + $extraParameters
		);
	}

	/**
	 * Get the avatar url for the given user.
	 *
	 * @param UserIdentity $userIdentity
	 * @param int $size Size of the avatar
	 * @return string
	 */
	public function getAvatarForUser(
		UserIdentity $userIdentity,
		int $size = 0
	): string {
		$email = $this->lookupEmailAddress( $userIdentity );
		$url = $this->gravatarServer . 'avatar/';

		if ( $email ) {
			$url .= md5( strtolower( trim( $email ) ) );
		}

		$queryParameters = [
			'r' => $this->acceptedAvatarRating,
			'd' => $this->defaultAvatar
		];

		if ( $size > 0 ) {
			$queryParameters['s'] = $size;
		}

		return wfAppendQuery( $url, $queryParameters );
	}

	/**
	 * Lookup the email address for the given user.
	 * When the user has not enabled gravatar avatars, null is returned.
	 *
	 * @param UserIdentity $userIdentity
	 * @return string|null
	 */
	private function lookupEmailAddress( UserIdentity $userIdentity ): ?string {
		if ( $this->userOptionsLookup->getBoolOption( $userIdentity, 'gravatar-use-gravatar' ) ) {
			$email = $this->userFactory->newFromUserIdentity( $userIdentity )->getEmail();

			if ( $email !== '' ) {
				return $email;
			}
		}

		return null;
	}
}
