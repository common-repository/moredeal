<?php

namespace Moredeal;

defined( '\ABSPATH' ) || exit;

/**
 * AutoLoader class file
 */
class AutoLoader {

	private static $base_dir;

	private static array $classMap = array();

	public function __construct() {

		self::$base_dir = PLUGIN_PATH;
		$this->register_auto_loader();
	}

	public function register_auto_loader() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Implementations of PSR-4
	 * @link: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
	 */
	public static function autoload( $className ) {
		$prefix = __NAMESPACE__ . '\\';
		$len = strlen( $prefix );

		if ( strncmp( $prefix, $className, $len ) !== 0 ) {
			// no, move to the next registered autoloader
			return;
		}

		// trying map autoloader first
		if ( isset( self::$classMap[ $className ] ) ) {
			include( self::$base_dir . self::$classMap[ $className ] );
		}

		// get the relative class name
		$relative_class = substr( $className, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = self::$base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

}

new AutoLoader();
