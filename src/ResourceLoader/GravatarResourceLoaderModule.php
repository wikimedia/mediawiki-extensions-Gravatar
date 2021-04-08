<?php

namespace MediaWiki\Extensions\Gravatar\ResourceLoader;

use Config;
use MediaWiki\Extensions\Gravatar\GravatarLookup;
use MediaWiki\MediaWikiServices;
use ResourceLoaderContext;
use ResourceLoaderFileModule;
use Wikimedia\Minify\CSSMin;
use function array_diff_key;
use function array_fill_keys;

class GravatarResourceLoaderModule extends ResourceLoaderFileModule {
	/** @inheritDoc */
	protected $origin = self::ORIGIN_CORE_INDIVIDUAL;

	/** @var GravatarLookup */
	private $gravatarLookup;

	/**
	 * @inheritDoc
	 *
	 * @param array $options
	 * @param string|null $localBasePath
	 * @param string|null $remoteBasePath
	 * @param GravatarLookup|null $gravatarLookup
	 * @param Config|null $gravatarConfig
	 */
	public function __construct(
		array $options = [],
		?string $localBasePath = null,
		?string $remoteBasePath = null,
		?GravatarLookup $gravatarLookup = null,
		?Config $gravatarConfig = null
	) {
		parent::__construct( $options, $localBasePath, $remoteBasePath );

		$this->gravatarLookup = $gravatarLookup ?? MediaWikiServices::getInstance()
				->getService( 'GravatarLookup' );
		$gravatarConfig = $gravatarConfig ?? MediaWikiServices::getInstance()->getConfigFactory()
				->makeConfig( 'Gravatar' );
		$this->skinStyles = array_diff_key(
			$this->skinStyles,
			array_fill_keys( $gravatarConfig->get( 'GravatarIgnoredSkins' ), true )
		);
	}

	/**
	 * @inheritDoc
	 *
	 * When making changes here, don't forget to update variables-sample-file.less to keep IDE
	 * support in sync with ResourceLoader!
	 */
	public function getLessVars( ResourceLoaderContext $context ) : array {
		$avatarUrl = CSSMin::buildUrlValue(
			$this->gravatarLookup->getAvatarForUser( $context->getUserObj() )
		);

		return parent::getLessVars( $context ) + [
			'gravatar-avatar-url' => $avatarUrl
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getDefinitionSummary( ResourceLoaderContext $context ) : array {
		$summary = parent::getDefinitionSummary( $context );

		$summary[] = [
			'AvatarUrl' => $this->gravatarLookup->getAvatarForUser( $context->getUserObj() )
		];

		return $summary;
	}
}