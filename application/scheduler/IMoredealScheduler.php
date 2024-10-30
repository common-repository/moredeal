<?php

namespace Moredeal\application\scheduler;

defined( '\ABSPATH' ) || exit;

/**
 * MoredealScheduler interface file
 */
interface IMoredealScheduler {

	/**
	 * Cron
	 * @return mixed
	 */
	public static function getCronTag();

	/**
	 * Run
	 *
	 * @return mixed
	 */
	public static function execute();
}
