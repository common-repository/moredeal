<?php

namespace Moredeal\application\scheduler;

defined( '\ABSPATH' ) || exit;

/**
 * MoredealScheduler class file
 */
abstract class MoredealScheduler implements IMoredealScheduler {

	public static function initAction() {
		add_action( static::getCronTag(), array( get_called_class(), 'execute' ) );
	}

	/**
	 * Add MoredealScheduler event hourly
	 *
	 * @param string $recurrence
	 * @param $timestamp
	 *
	 * @return void
	 */
	public static function addMoredealScheduleEvent( string $recurrence = 'hourly', $timestamp = null ) {
		if ( ! wp_next_scheduled( static::getCronTag() ) ) {
			if ( ! $timestamp ) {
				$timestamp = time();
			}
			wp_schedule_event( $timestamp, $recurrence, static::getCronTag() );
			error_log('addMoredealScheduleEvent' . '  time: ' . date_i18n( 'Y-m-d H:i:s' ));
		}
	}

	/**
	 * Clear MoredealScheduler event
	 * @return void
	 */
	public static function clearMoredealScheduleEvent() {
		if ( wp_next_scheduled( static::getCronTag() ) ) {
			wp_clear_scheduled_hook( static::getCronTag() );
			error_log('clearMoredealScheduleEvent' . '  time: ' . date_i18n( 'Y-m-d H:i:s' ));
		}
	}

}
