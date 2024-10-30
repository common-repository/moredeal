<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\AdminPlugin;
use Moredeal\application\admin\GeneralConfig;
use Moredeal\application\helpers\TextHelper;
use Moredeal\application\Plugin;

/**
 * Module abstract class file
 */
abstract class Module {

	private $id;
	private $dir;
	protected $is_active;
	protected $name;
	protected $description;
	private $is_configurable;
	private $docs_uri;

	public function __construct( $module_id = null ) {
		if ( $module_id ) {
			$this->id = $module_id;
		} else {
			$this->id = static::getIdStatic();
		}

		$info = $this->info();
		if ( ! empty( $info['name'] ) ) {
			$this->name = $info['name'];
		} else {
			$this->name = $this->id;
		}
		if ( ! empty( $info['description'] ) ) {
			$this->description = $info['description'];
		}
		if ( ! empty( $info['docs_uri'] ) ) {
			$this->docs_uri = $info['docs_uri'];
		}
	}

	public function info(): array {
		return array();
	}

	public final function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getDir(): string {
		if ( $this->dir === null ) {
			$rc        = new \ReflectionClass( get_class( $this ) );
			$this->dir = dirname( $rc->getFileName() ) . DIRECTORY_SEPARATOR;
		}

		return $this->dir;
	}

	public function isActive(): bool {
		return $this->is_active;
	}

	public function isDeprecated(): bool {
		return false;
	}

	public function isConfigurable(): bool {
		if ( $this->is_configurable === null ) {
			if ( is_file( $this->getDir() . $this->getMyPathId() . 'Config.php' ) ) {
				$this->is_configurable = true;
			} else {
				$this->is_configurable = false;
			}
		}

		return $this->is_configurable;
	}

	public function isNew(): bool {
		if ( ! $module_version = $this->releaseVersion() ) {
			return false;
		}

		$module_version = join( '.', array_slice( explode( '.', $module_version ), 0, 2 ) );
		$plugin_version = join( '.', array_slice( explode( '.', Plugin::version() ), 0, 2 ) );
		if ( $module_version == $plugin_version ) {
			return true;
		} else {
			return false;
		}
	}

	public function releaseVersion(): string {
		return '';
	}

	public function isFree(): bool {
		return false;
	}

	/**
	 * @throws \Exception
	 */
	public function getConfigInstance() {
		return ModuleManager::configFactory( $this->getId() );
	}

	/**
	 * @throws \Exception
	 */
	public function config( $opt_name, $default = null ) {
		if ($this->getId() == "Amazon") {
			return GeneralConfig::getInstance()->option( 'amazon_' . $opt_name, $default );
		}
//		if ( ! $this->getConfigInstance()->option_exists( $opt_name ) ) {
//			return $default;
//		} else {
//			return $this->getConfigInstance()->option( $opt_name );
//		}
	}

	public function render( $view_name, $_data = null ) {
		if ( is_array( $_data ) ) {
			extract( $_data, EXTR_PREFIX_SAME, 'data' );
		} else {
			$data = $_data;
		}
		$base = \Moredeal\PLUGIN_PATH . 'application/modules/';

		include $base . $this->getMyPathId() . '/views/' . TextHelper::clear( $view_name ) . '.php';
	}

	public function requirements() {
		return '';
	}

	public function getJsUri(): string {
		return plugins_url( '\application\modules\\' . $this->getMyPathId() . '\js', \Moredeal\PLUGIN_FILE );
	}

	public function getDocsUri() {
		return $this->docs_uri;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getMyPathId() {
		return self::getPathId( $this->getId() );
	}

	public function getMyShortId() {
		return self::getShortId( $this->getId() );
	}

	public static function getPathId( $module_id ) {
		// AE or Feed module?
		$parts = explode( '__', $module_id );

		return $parts[0];
	}

	public function getShortId( $module_id ) {
		// AE or Feed module?
		$parts = explode( '__', $module_id );
		if ( count( $parts ) == 2 ) {
			return $parts[1];
		} else {
			return $module_id;
		}
	}

	public function renderMetaboxModule() {
		AdminPlugin::render( 'metabox_module', array( 'module_id' => $this->getId(), 'module' => $this ) );
	}

	public static function getIdStatic() {
		$parts = explode( '\\', get_called_class() );

		return $parts[ count( $parts ) - 2 ];
	}

	public function getStatusText(): string {
		if ( $this->isActive() && $this->isDeprecated() ) {
			return 'deprecated';
		} elseif ( $this->isActive() ) {
			return 'active';
		} else {
			return 'inactive';
		}
	}

	abstract function getUrlAssociateTagParam();

}
