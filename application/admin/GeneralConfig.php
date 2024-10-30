<?php

namespace Moredeal\application\admin;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\Config;
use Moredeal\application\helpers\TextHelper;
use Moredeal\application\Plugin;

class GeneralConfig extends Config {

	/**
	 * 添加菜单
	 * @return void
	 */
	public function add_admin_menu() {
		add_submenu_page( Plugin::slug, __( 'Setting', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'Setting', 'moredeal' ), 'manage_options', $this->page_slug, array(
			$this,
			'settings_page'
		) );
	}

	/**
	 * 加载设置页面
	 * @return void
	 */
	public function settings_page() {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'moredeal-admin-ui-css', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version );
		AdminPlugin::render( 'settings', array( 'page_slug' => $this->page_slug() ) );
	}

	/**
	 * @return string
	 */
	public function page_slug(): string {
		return Plugin::slug;
	}

	/**
	 * @return string
	 */
	public function option_name(): string {
		return 'moredeal_options';
	}

	/**
	 * 操作选项
	 * @return array[]
	 */
	protected function options(): array {

		$post_types = get_post_types( array( 'public' => true ) );
		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		$options                       = array(
			// 显示库存状态
			'show_stock_status'    => array(
				'title'            => __( 'Stock status', 'moredeal' ),
				'description'      => __( 'How to deal with stock status for product', 'moredeal' ),
				'callback'         => array( $this, 'render_dropdown' ),
				'style'            => 'width: 300px',
				'dropdown_options' => array(
					'show_status'       => __( 'Show stock status', 'moredeal' ),
					'hide_status'       => __( 'Hide stock status', 'moredeal' ),
					'show_out_of_stock' => __( 'Show OutOfStock status only', 'moredeal' ),
					'show_in_stock'     => __( 'Show InStock status only', 'moredeal' ),
				),
				'default'          => 'show_status',
				'section'          => __( 'General settings', 'moredeal' ),
			),
			// 商品更新
			'out_of_stock_product' => array(
				'title'            => __( 'Out of Stock products', 'moredeal' ),
				'description'      => __( 'How to deal with Out of Stock products.', 'moredeal' ),
				'callback'         => array( $this, 'render_dropdown' ),
				'style'            => 'width: 300px',
				'dropdown_options' => array(
					''             => __( 'Do nothing', 'moredeal' ),
					'hide_price'   => __( 'Hide price', 'moredeal' ),
					'hide_product' => __( 'Hide product', 'moredeal' ),
				),
				'default'          => '',
				'section'          => __( 'General settings', 'moredeal' ),
			),
			// ---------------------------------------------------------------------------------------------------------
			// 标签方式
			'rel_attribute'        => array(
				'title'            => __( 'Rel attribute for affiliate links', 'moredeal' ),
				'description'      => sprintf( '<a target="_blank" href="%s">' . __( 'Qualify', 'moredeal' ) . '</a>' . __( ' your affiliate links to Google.', 'moredeal' ), 'https://support.google.com/webmasters/answer/96569' ),
				'checkbox_options' => array(
					'nofollow'   => 'nofollow',
					'sponsored'  => 'sponsored',
//					'external'   => 'external',
//					'noopener'   => 'noopener',
//					'noreferrer' => 'noreferrer',
					'ugc'        => 'ugc',
				),
				'callback'         => array( $this, 'render_checkbox_list' ),
				'default'          => array( 'nofollow' ),
				'section'          => __( 'General settings', 'moredeal' ),
			),
			// 按钮颜色
			'button_color'         => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '#d9534f',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'General settings', 'moredeal' ),
			),
			// 价格颜色
			'price_color'          => array(
				'title'       => __( 'Price color', 'moredeal' ),
				'description' => __( 'Price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#dc3545',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'General settings', 'moredeal' ),
			),
			// 按钮 text
			'btn_text_buy_now'     => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'General settings', 'moredeal' ),
			),
			// ---------------------------------------------------------------------------------------------------------
			'merchants'            => array(
				'title'     => __( 'Merchant settings', 'moredeal' ),
				'callback'  => array( $this, 'render_merchants_block' ),
				'validator' => array(
					array(
						'call' => array( $this, 'formatMerchantFields' ),
						'type' => 'filter',
					),
				),
				'default'   => array(),
				'section'   => __( 'General settings', 'moredeal' ),
			),
			// ---------------------------------------------------------------------------------------------------------
//			'seastar_api_host'  => array(
//				'title'       => __( 'Seastar Api Host', 'moredeal' ),
//				'description' => __( 'Seastar Api Url To Use', 'moredeal' ),
//				'callback'    => array( $this, 'render_input' ),
//				'default'     => '',
//				'style'       => 'width: 600px',
//				'validator'   => array(
//					'strip_tags',
//				),
//				'section'     => __( 'Debugger', 'moredeal' ),
//			),
			'amazon_is_active'     => array(
				'title'       => __( 'Enable module', 'moredeal' ),
				'description' => '',
				'callback'    => array( $this, 'render_checkbox' ),
				'default'     => true,
				'section'     => __( 'Amazon Module', 'moredeal' ),
//				'validator'   => array(
//					array(
//						'call'    => array( $this, 'checkRequirements' ),
//						'message' => __( 'Could not activate.', 'moredeal' ),
//					),
//				),
			),
			'amazon_associate_tag' => array(
				'title'       => __( 'Default Associate Tag', 'moredeal' ) . ' <span class="moredeal_required">*</span>',
				'description' => __( 'An alphanumeric token that uniquely identifies you as an Associate. To obtain an Associate Tag, refer to ', 'moredeal' ) . '<a target="_blank" href="https://docs.aws.amazon.com/AWSECommerceService/latest/DG/becomingAssociate.html">' . __( 'Becoming an Associate', 'moredeal' ) . '</a>.',
				'callback'    => array( $this, 'render_input' ),
				'validator'   => array(
					'trim',
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'required' ),
						'when'    => 'amazon_is_active',
						'message' => __( 'The "Default Associate Tag" can not be empty.', 'moredeal' ),
					),
				),
				'section'     => __( 'Amazon Module', 'moredeal' ),
				'metaboxInit' => true,
			),
			'amazon_locale'          => array(
				'title'            => __( 'Default locale', 'moredeal' ) . ' <span class="moredeal_required">*</span>',
				'description'      => __( 'The branch/locale of Amazon. Each branch requires a separate registration in certain affiliate program.', 'moredeal' ),
				'callback'         => array( $this, 'render_dropdown' ),
				'dropdown_options' => self::getLocalesList(),
				'default'          => self::getDefaultLocale(),
				'style'            => 'width: 350px',
				'validator'        => array(
					'trim',
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'required' ),
						'when'    => 'amazon_is_active',
						'message' => __( 'The "Local" can not be empty.', 'moredeal' ),
					),
				),
				'section'          => __( 'Amazon Module', 'moredeal' ),
				'metaboxInit'      => true,
			),
			'amazon_priority'        => array(
				'title'       => __( 'Priority', 'moredeal' ),
				'description' => __( 'Priority sets order of modules in post. 0 - is the most highest priority.', 'moredeal' ),
				__( 'Also it applied to price sorting.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 10,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => __( 'Amazon Module', 'moredeal' ),
			),
			'amazon_ttl_items'       => array(
				'title'       => __( 'Price update', 'moredeal' ),
				'description' => __( 'Time in seconds for updating prices, availability, etc. 0 - never update', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 86400,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => __( 'Amazon Module', 'moredeal' ),
			),
			'amazon_update_mode'     => array(
				'title'            => __( 'Update mode', 'moredeal' ),
				'description'      => __( 'Product update mode, You can chose update by Page view or Cron', 'moredeal' ),
				'callback'         => array( $this, 'render_dropdown' ),
				'style'            => 'width: 350px',
				'dropdown_options' => array(
					'visit'      => __( 'Page view', 'moredeal' ),
					'cron'       => __( 'Cron', 'moredeal' ),
					'visit_cron' => __( 'Page view + Cron', 'moredeal' ),
				),
				'default'          => 'visit',
				'section'          => __( 'Amazon Module', 'moredeal' ),
			),
			'amazon_disclaimer_text' => array(
				'title'       => __( 'Amazon disclaimer', 'moredeal' ),
				'callback'    => array( $this, 'render_textarea' ),
				'description' => __( 'Text of Amazon Disclaimer', 'moredeal' ),
				'default'     => '',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Amazon Module', 'moredeal' ),
			),

		);
		foreach ( self::getLocalesList() as $locale_id => $locale_name ) {
			$options[ 'amazon_associate_tag_' . $locale_id ] = array(
				'title'       => sprintf( __( 'Associate Tag for %s', 'moredeal' ), $locale_name ),
				'description' => sprintf( __( 'Type here your tracking ID for this %s Associate Tag', 'moredeal' ), $locale_name ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Amazon Module', 'moredeal' ),
				'metaboxInit' => true,
			);
		}


		return $options;
	}

	/**
	 * 语言列表
	 * @return string[]
	 */
	public static function getLocalesList(): array {
		return array(
			'us' => 'US',
			'uk' => 'UK',
			'de' => 'DE',
			'jp' => 'JP',
			'cn' => 'CN',
			'fr' => 'FR',
			'it' => 'IT',
			'es' => 'ES',
			'ca' => 'CA',
			'br' => 'BR',
			'in' => 'IN',
			'mx' => 'MX',
			'au' => 'AU'
		);
	}

	/**
	 * 默认的语言
	 * @return string
	 */
	public static function getDefaultLocale(): string {
		return 'us';
	}

	/**
	 * 默认语言
	 * @return mixed|string
	 */
	public static function getDefaultLang() {
		$locale = get_locale();
		$lang   = explode( '_', $locale );
		if ( array_key_exists( $lang[0], self::langs() ) ) {
			return $lang[0];
		} else {
			return 'en';
		}
	}

	/**
	 * render_logo_fields_line
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_logo_fields_line( $args ) {
		$i     = $args['_field'] ?? 0;
		$name  = $args['value'][ $i ]['name'] ?? '';
		$value = $args['value'][ $i ]['value'] ?? '';

		echo '<input name="' . esc_attr( $args['option_name'] ) . '['
		     . esc_attr( $args['name'] ) . '][' . esc_attr( $i ) . '][name]" value="'
		     . esc_attr( $name ) . '" class="text" placeholder="' . esc_attr( __( 'Domain name', 'moredeal' ) ) . '"  type="text"/>';
		echo '<input name="' . esc_attr( $args['option_name'] ) . '['
		     . esc_attr( $args['name'] ) . '][' . esc_attr( $i ) . '][value]" value="'
		     . esc_attr( $value ) . '" class="regular-text ltr" placeholder="' . esc_attr( __( 'Logo URL', 'moredeal' ) ) . '"  type="text"/>';
	}

	/**
	 * render_logo_fields_block
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_logo_fields_block( $args ) {
		if ( is_array( $args['value'] ) ) {
			$total = count( $args['value'] ) + 3;
		} else {
			$total = 3;
		}

		for ( $i = 0; $i < $total; $i ++ ) {
			echo '<div style="padding-bottom: 5px;">';
			$args['_field'] = $i;
			$this->render_logo_fields_line( $args );
			echo '</div>';
		}
		if ( $args['description'] ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * formatLogoFields
	 *
	 * @param $values
	 *
	 * @return array
	 */
	public function formatLogoFields( $values ): array {
		$results = array();
		foreach ( $values as $k => $value ) {
			$name = trim( sanitize_text_field( $value['name'] ) );
			if ( $host = TextHelper::getHostName( $values[ $k ]['name'] ) ) {
				$name = $host;
			}

			$value = trim( sanitize_text_field( $value['value'] ) );

			if ( ! $name || ! $value ) {
				continue;
			}

			if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
				continue;
			}

			if ( in_array( $name, array_column( $results, 'name' ) ) ) {
				continue;
			}

			$result    = array( 'name' => $name, 'value' => $value );
			$results[] = $result;
		}

		return $results;
	}

	/**
	 * render_translation_row
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_translation_row( $args ) {
		$field_name = $args['_field_name'];
		$value      = $args['value'][ $field_name ] ?? '';

		echo '<input value="' . esc_attr( $field_name ) . '" class="regular-text ltr" type="text" readonly />';
		echo ' &#x203A; ';
		echo '<input name="' . esc_attr( $args['option_name'] ) . '['
		     . esc_attr( $args['name'] ) . '][' . esc_attr( $field_name ) . ']" value="'
		     . esc_attr( $value ) . '" class="regular-text ltr" placeholder="' . esc_attr( __( 'Translated string', 'moredeal' ) ) . '"  type="text"/>';
	}

	/**
	 * render_translation_block
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_translation_block( $args ) {
		if ( ! $args['value'] ) {
			$args['value'] = array();
		}

		foreach ( array_keys( self::frontendTexts() ) as $str ) {
			echo '<div style="padding-bottom: 5px;">';
			$args['_field_name'] = $str;
			$this->render_translation_row( $args );
			echo '</div>';
		}
		if ( $args['description'] ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * render_merchant_line
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_merchant_line( $args ) {
		$i     = $args['_field'] ?? 0;
		$name  = $args['value'][ $i ]['name'] ?? '';
		$value = $args['value'][ $i ]['shop_info'] ?? '';

		echo '<input style="margin-bottom: 5px; width: 500px" name="' . esc_attr( $args['option_name'] ) . '['
		     . esc_attr( $args['name'] ) . '][' . esc_attr( $i ) . '][name]" value="'
		     . esc_attr( $name ) . '" class="regular-text ltr" placeholder="' . esc_attr( __( 'Domain name', 'moredeal' ) ) . '"  type="text"/>';

		echo '<br>';
		echo '<textarea rows="2" style="margin-bottom: 5px; width: 750px; height: 80px" name="' . esc_attr( $args['option_name'] ) . '['
		     . esc_attr( $args['name'] ) . '][' . esc_attr( $i ) . '][shop_info]" value="'
		     . esc_attr( $value ) . '" class="large-text code" placeholder="' . esc_attr( __( 'Shop info', 'moredeal' ) ) . '"  type="text">' . esc_html( $value ) . '</textarea>';
	}

	/**
	 * render_merchants_block
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_merchants_block( $args ) {
//		if ( is_array( $args['value'] ) ) {
//			$total = count( $args['value'] ) + 3;
//		} else {
//			$total = 3;
//		}
//
//		for ( $i = 0; $i < $total; $i ++ ) {
//			echo '<div style="padding-bottom: 20px;">';
//			$args['_field'] = $i;
//			$this->render_merchant_line( $args );
//			echo '</div>';
//		}
		echo '<div style="padding-bottom: 20px;">';
//		$args['_field'] = $i;
		$this->render_merchant_line( $args );
		echo '</div>';
		if ( $args['description'] ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * formatMerchantFields
	 *
	 * @param $values
	 *
	 * @return array
	 */
	public function formatMerchantFields( $values ): array {
		$results = array();
		foreach ( $values as $k => $value ) {
			$name = strtolower( trim( sanitize_text_field( $value['name'] ) ) );
			if ( $host = TextHelper::getHostName( $value['name'] ) ) {
				$name = $host;
			}

			if ( ! $name ) {
				continue;
			}

			if ( in_array( $name, array_column( $results, 'name' ) ) ) {
				continue;
			}

			$shop_info = TextHelper::nl2br( trim( TextHelper::sanitizeHtml( $value['shop_info'] ) ) );

			$result    = array( 'name' => $name, 'shop_info' => $shop_info );
			$results[] = $result;
		}

		return $results;
	}

	/**
	 * frontendTextsSanitize
	 *
	 * @param $values
	 *
	 * @return mixed
	 */
	public function frontendTextsSanitize( $values ) {
		foreach ( $values as $k => $value ) {
			$values[ $k ] = trim( sanitize_text_field( $value ) );
		}

		return $values;
	}

	/**
	 * frontendTexts function.
	 * @return array
	 */
	private static function frontendTexts(): array {
		return array(
			'in stock'                                              => __( 'in stock', 'moredeal' ),
			'out of stock'                                          => __( 'out of stock', 'moredeal' ),
			'Last updated on %s'                                    => __( 'Last updated on %s', 'moredeal' ),
			'Last Amazon price update was: %s'                      => __( 'Last Amazon price update was: %s', 'moredeal' ),
			'as of %s'                                              => __( 'as of %s', 'moredeal' ),
			'%d new from %s'                                        => __( '%d new from %s', 'moredeal' ),
			'%d used from %s'                                       => __( '%d used from %s', 'moredeal' ),
			'Free shipping'                                         => __( 'Free shipping', 'moredeal' ),
			'OFF'                                                   => __( 'OFF', 'moredeal' ),
			'Plus %s Cash Back'                                     => __( 'Plus %s Cash Back', 'moredeal' ),
			'Price'                                                 => __( 'Price', 'moredeal' ),
			'Features'                                              => __( 'Features', 'moredeal' ),
			'Specifications'                                        => __( 'Specifications', 'moredeal' ),
			'Statistics'                                            => __( 'Statistics', 'moredeal' ),
			'Current Price'                                         => __( 'Current Price', 'moredeal' ),
			'Highest Price'                                         => __( 'Highest Price', 'moredeal' ),
			'Lowest Price'                                          => __( 'Lowest Price', 'moredeal' ),
			'Since %s'                                              => __( 'Since %s', 'moredeal' ),
			'Last price changes'                                    => __( 'Last price changes', 'moredeal' ),
			'Start date: %s'                                        => __( 'Start date: %s', 'moredeal' ),
			'End date: %s'                                          => __( 'End date: %s', 'moredeal' ),
			'Set Alert for'                                         => __( 'Set Alert for', 'moredeal' ),
			'Price History for'                                     => __( 'Price History for', 'moredeal' ),
			'Create Your Free Price Drop Alert!'                    => __( 'Create Your Free Price Drop Alert!', 'moredeal' ),
			'Wait For A Price Drop'                                 => __( 'Wait For A Price Drop', 'moredeal' ),
			'Your Email'                                            => __( 'Your Email', 'moredeal' ),
			'Desired Price'                                         => __( 'Desired Price', 'moredeal' ),
			'SET ALERT'                                             => __( 'SET ALERT', 'moredeal' ),
			'You will receive a notification when the price drops.' => __( 'You will receive a notification when the price drops.', 'moredeal' ),
			'I agree to the %s.'                                    => __( 'I agree to the %s.', 'moredeal' ),
			'Privacy Policy'                                        => __( 'Privacy Policy', 'moredeal' ),
			'Sorry. No products found.'                             => __( 'Sorry. No products found.', 'moredeal' ),
			'Search Results for "%s"'                               => __( 'Search Results for "%s"', 'moredeal' ),
			'Price per unit: %s'                                    => __( 'Price per unit: %s', 'moredeal' ),
			'today'                                                 => __( 'today', 'moredeal' ),
			'%d day ago'                                            => __( '%d day ago', 'moredeal' ),
			'%d days ago'                                           => __( '%d days ago', 'moredeal' ),
		);
	}

	/**
	 * 语言列表
	 * @return array
	 */
	public static function langs(): array {
		return array(
			'ar'    => 'Arabic (ar)',
			'bg'    => 'Bulgarian (bg)',
			'ca'    => 'Catalan (ca)',
			'zh_CN' => 'Chinese (zh_CN)',
			'zh_TW' => 'Chinese (zh_TW)',
			'hr'    => 'Croatian (hr)',
			'cs'    => 'Czech (cs)',
			'da'    => 'Danish (da)',
			'nl'    => 'Dutch (nl)',
			'en'    => 'English (en)',
			'et'    => 'Estonian (et)',
			'tl'    => 'Filipino (tl)',
			'fi'    => 'Finnish (fi)',
			'fr'    => 'French (fr)',
			'de'    => 'German (de)',
			'el'    => 'Greek (el)',
			'iw'    => 'Hebrew (iw)',
			'hi'    => 'Hindi (hi)',
			'hu'    => 'Hungarian (hu)',
			'is'    => 'Icelandic (is)',
			'id'    => 'Indonesian (id)',
			'it'    => 'Italian (it)',
			'ja'    => 'Japanese (ja)',
			'ko'    => 'Korean (ko)',
			'lv'    => 'Latvian (lv)',
			'lt'    => 'Lithuanian (lt)',
			'ms'    => 'Malay (ms)',
			'no'    => 'Norwegian (no)',
			'fa'    => 'Persian (fa)',
			'pl'    => 'Polish (pl)',
			'pt'    => 'Portuguese (pt)',
			'br'    => 'Portuguese (br)',
			'ro'    => 'Romanian (ro)',
			'ru'    => 'Russian (ru)',
			'sr'    => 'Serbian (sr)',
			'sk'    => 'Slovak (sk)',
			'sl'    => 'Slovenian (sl)',
			'es'    => 'Spanish (es)',
			'sv'    => 'Swedish (sv)',
			'th'    => 'Thai (th)',
			'tr'    => 'Turkish (tr)',
			'uk'    => 'Ukrainian (uk)',
			'ur'    => 'Urdu (ur)',
			'vi'    => 'Vietnamese (vi)',
		);
	}


}