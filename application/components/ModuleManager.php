<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\TextHelper;
use Moredeal\application\Plugin;

/**
 * ModuleManager class file
 */
class ModuleManager {

	const DEFAULT_MODULES_DIR = 'application/modules';

	/**
	 * @var array of Module
	 */
	private static array $modules = array();

	/**
	 * $active_modules
	 * @var array of Module
	 */
	private static array $active_modules = array();

	/**
	 * @var array of Module
	 */
	private static array $configs = array();

	private static ?ModuleManager $instance = null;

	public static function getInstance(): ?ModuleManager {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @throws \Exception
	 */
	private function __construct() {
		$this->initModules();
	}

	/**
	 * @throws \Exception
	 */
	private function initModules() {
		$modules_ids = $this->scanForDefaultModules();
		$modules_ids = apply_filters( 'moredeal_modules', $modules_ids );
		// create modules
		foreach ( $modules_ids as $module_id ) {
			// create module
			self::factory( $module_id );
		}
		// fill active modules
		foreach ( self::$modules as $module ) {
			if ( $module->isActive() ) {
				self::$active_modules[ $module->getId() ] = $module;
			}
		}
	}

	/**
	 * adminInit
	 * @return void
	 * @throws \Exception
	 */
	public function adminInit() {
		foreach ( $this->getConfigurableModules() as $module ) {
			$config = self::configFactory( $module->getId() );
			$config->adminInit();
		}
	}

	/**
	 * 可以更新配置的模块
	 * @return array
	 */
	public function getUpdateModuleIds(): array {
		$result = array();
		foreach ( $this->getAffiliateParsers( true ) as $module ) {
			if ( ! $module->isItemsUpdateAvailable() || ! $module->config( 'ttl_items' ) ) {
				continue;
			}

			if ( $module->config( 'update_mode' ) == 'cron' || $module->config( 'update_mode' ) == 'visit_cron' ) {
				$result[] = $module->getId();
			}
		}

		return $result;
	}

	/**
	 * 允许页面更新的模块
	 * @return array
	 */
	public function getVisitUpdateModuleIds(): array {
		$result = array();
		foreach ( $this->getAffiliateParsers( true ) as $module ) {
			if ( $module->config( 'update_mode' ) == 'visit' || $module->config( 'update_mode' ) == 'visit_cron' ) {
				$result[] = $module->getId();
			}
		}

		return $result;
	}

	/**
	 *  Highlight the proper submenu item
	 */
	public function highlightAdminMenu( $parent_file ) {

		global $plugin_page;

		if ( substr( $plugin_page, 0, strlen( Plugin::slug ) ) !== Plugin::slug ) {
			return $parent_file;
		}

		return $parent_file;
	}

	private function scanForDefaultModules(): ?array {
		$path = \Moredeal\PLUGIN_PATH . self::DEFAULT_MODULES_DIR . DIRECTORY_SEPARATOR;

		return $this->scanForModules( $path );
	}

	private function scanForModules( $path ): ?array {
		$folder_handle = @opendir( $path );
		if ( $folder_handle === false ) {
			return null;
		}
		$founded_modules = array();
		while ( ( $m_dir = readdir( $folder_handle ) ) !== false ) {
			if ( $m_dir == '.' || $m_dir == '..' ) {
				continue;
			}
			$module_path = $path . $m_dir;
			if ( ! is_dir( $module_path ) ) {
				continue;
			}

			$module_id         = $m_dir;
			$founded_modules[] = TextHelper::clear( $module_id );
		}
		closedir( $folder_handle );
		return $founded_modules;
	}

	/**
	 * @throws \Exception
	 */
	public static function factory( $module_id ) {
		if ( ! isset( self::$modules[ $module_id ] ) ) {
			$path_prefix  = Module::getPathId( $module_id );
			$module_class = 'Moredeal\\application\\modules\\' . $path_prefix . "\\" . $path_prefix . 'Module';
			if ( class_exists( $module_class) === false ) {
				throw new \Exception( "Unable to load module class: '$module_class'." );
			}

			$module = new $module_class( $module_id );

			if ( ! ( $module instanceof Module ) ) {
				throw new \Exception( "The module '$module_id' must inherit from Module." );
			}

			if ( Plugin::isFree() && ! $module->isFree() ) {
				return false;
			}

			self::$modules[ $module_id ] = $module;
		}

		return self::$modules[ $module_id ];
	}

	/**
	 * @throws \Exception
	 */
	public static function parserFactory( $module_id ) {
		$module = self::factory( $module_id );
		if ( ! ( $module instanceof ParserModule ) ) {
			throw new \Exception( "The parser module '{$module_id}' must inherit from ParserModule." );
		}

		return $module;
	}

	/**
	 * @throws \Exception
	 */
	public static function configFactory( $module_id ) {
		if ( ! isset( self::$configs[ $module_id ] ) ) {
			$path_prefix = Module::getPathId( $module_id );

			$config_class = "Moredeal\\application\\modules\\" . $path_prefix . "\\" . $path_prefix . 'Config';

			if ( class_exists( $config_class ) === false ) {
				throw new \Exception( "Unable to load module config class: '$config_class'." );
			}

			$config = $config_class::getInstance( $module_id );

			if ( self::factory( $module_id )->isParser() ) {
				if ( ! ( $config instanceof ParserModuleConfig ) ) {
					throw new \Exception( "The parser module config '{$config_class}' must inherit from ParserModuleConfig." );
				}
			} else {
				if ( ! ( $config instanceof ModuleConfig ) ) {
					throw new \Exception( "The module config '$config_class' must inherit from ModuleConfig." );
				}
			}

			self::$configs[ $module_id ] = $config;
		}

		return self::$configs[ $module_id ];
	}

	public function getModules( $only_active = false ): array {
		if ( $only_active ) {
			return self::$active_modules;
		} else {
			return self::$modules;
		}
	}

	public function AmazonModule(): ?Module {
		return self::$active_modules['Amazon'];
	}

	public function getModulesIdList( $only_active = false ): array {
		return array_keys( $this->getModules( $only_active ) );
	}

	public function getParserModules( $only_active = false ): array {
		$modules = $this->getModules( $only_active );
		$parsers = array();
		foreach ( $modules as $module ) {
			if ( $module->isParser() ) {
				$parsers[ $module->getId() ] = $module;
			}
		}

		return $parsers;
	}

	public function getParserModulesIdList( $only_active = false ): array {
		return array_keys( $this->getParserModules( $only_active ) );
	}

	public function getParserModulesByTypes( $types, $only_active = true ): array {
		if ( $types == 'ALL' ) {
			$types = null;
		}

		if ( $types && ! is_array( $types ) ) {
			$types = array( $types );
		}
		$res = array();
		foreach ( $this->getParserModules( $only_active ) as $module ) {
			if ( $types && ! in_array( $module->getParserType(), $types ) ) {
				continue;
			}
			$res[ $module->getId() ] = $module;
		}

		return $res;
	}

	public function getParserModuleIdsByTypes( $types, $only_active = true ): array {
		return array_keys( $this->getParserModulesByTypes( $types, $only_active ) );
	}

	public function getConfigurableModules( $active_only = false ): array {
		$result = array();
		foreach ( $this->getModules( $active_only ) as $module ) {
			if ( $module->isConfigurable() ) {
				$result[] = $module;
			}
		}

		return $result;
	}

	public function moduleExists( $module_id ): bool {
		if ( isset( self::$modules[ $module_id ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function isModuleActive( $module_id ): bool {
		if ( isset( self::$active_modules[ $module_id ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function getOptionsList(): array {
		$options = array();
		foreach ( $this->getConfigurableModules() as $module ) {
			$config                            = $module->getConfigInstance();
			$options[ $config->option_name() ] = $config->getOptionValues();
		}

		return $options;
	}

	public function getAffiliateParsers( $only_active = false, $only_product = false ): array {
		$modules = $this->getModules( $only_active );
		$parsers = array();
		foreach ( $modules as $module ) {
			if ( $only_product && strstr( $module->getId(), 'Coupons' ) ) {
				continue;
			}

			if ( ! $module->isAffiliateParser() ) {
				continue;
			}

			$parsers[ $module->getId() ] = $module;
		}

		return $parsers;
	}

	public function getAffiliteModulesList( $only_active = true ): array {
		$results = array();
		$modules = ModuleManager::getInstance()->getAffiliateParsers( $only_active );
		$feeds   = array();
		foreach ( $modules as $module_id => $module ) {
			$results[ $module_id ] = $module->getName();
		}
		return $results;
	}

}
