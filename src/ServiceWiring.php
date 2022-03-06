<?php

namespace MediaWiki\Extension\Gravatar;

use MediaWiki\MediaWikiServices;

return [
	'GravatarLookup' => static function ( MediaWikiServices $services ): GravatarLookup {
		$config = $services->getConfigFactory()->makeConfig( 'Gravatar' );

		return new GravatarLookup(
			$services->getUserFactory(),
			$services->getUserOptionsLookup(),
			$config->get( 'GravatarServer' ),
			$config->get( 'GravatarDefaultAvatar' ),
			$config->get( 'GravatarAcceptedAvatarRating' )
		);
	}
];
