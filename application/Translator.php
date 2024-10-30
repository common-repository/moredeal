<?php

namespace Moredeal\application;

defined('\ABSPATH') || exit;

/**
 * Translator class file
 */
class Translator {

	public static function __( $str ): ?string {
		return self::translate( $str );
	}

	public static function translate( $str ): ?string {

		return __( $str, 'moredeal' );
	}
}
