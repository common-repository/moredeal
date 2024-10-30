<?php

namespace Moredeal\application\admin;

use Moredeal\application\components\Config;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;

class HotKeywordsConfig extends Config {

	const slug = "moredeal-hotKeywords-config";

	public function add_admin_menu() {
		add_submenu_page( Plugin::slug, __( 'HotKeywords', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'HotKeywords', 'moredeal' ), 'manage_options', $this->page_slug, array(
			$this,
			'hotKeywordsIndex'
		) );
	}

	/**
	 * 加载 template 页面
	 * @return void
	 */
	public function hotKeywordsIndex(): void {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'moredeal-admin-ui-css', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version );
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css', false, Plugin::version );
		AdminPlugin::render( 'hot_keywords_index', array( 'page_slug' => $this->page_slug() ) );
	}

	/**
	 * 页面路由
	 * @return string
	 */
	public function page_slug(): string {
		return self::slug;
	}

	/**
	 * 配置名称
	 * @return string
	 */
	public function option_name(): string {
		return "moredeal_hot_keywords_options";
	}

	/**
	 * 配置选项
	 * @return array[]
	 */
	protected function options(): array {
		$options = array();

		foreach ( self::getHotKeywordsColumn() as $column_id => $column_name ) {
			$options[ $column_id ] = array(
				'title'       => __( $column_name, 'moredeal' ),
				'description' => $column_id == 'hot_keyword_1' ? __( "Please enter the hot keyword, it will be show when search product, you can enter text like 'watch' or 'watch,823', 'watch' is the hot keywords, '823' is the category id. You can get category id from " , 'moredeal' ) . __('get the category id', 'moredeal') : "",
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'section'     => __( 'Default', 'moredeal' ),
				'metaboxInit' => true,
			);
		}

		return $options;
	}

	private static function getHotKeywordsColumn(): array {
		return array(
			'hot_keyword_1'  => 'The First Hot Keyword',
			'hot_keyword_2'  => 'The Second Hot Keyword',
			'hot_keyword_3'  => 'The Third Hot Keyword',
			'hot_keyword_4'  => 'The Fourth Hot Keyword',
			'hot_keyword_5'  => 'The Fifth Hot Keyword',
			'hot_keyword_6'  => 'The Sixth Hot Keyword',
			'hot_keyword_7'  => 'The Seventh Hot Keyword',
			'hot_keyword_8'  => 'The Eighth Hot Keyword',
			'hot_keyword_9'  => 'The Ninth Hot Keyword',
			'hot_keyword_10' => 'The Tenth Hot Keyword',
		);
	}

	/**
	 * 获取热门关键词
	 * @return array
	 */
	public static function getHotKeywords(): array {
		$hotKeywords = array();
		foreach ( self::getHotKeywordsColumn() as $column_id => $column_name ) {
			$hotKeyWord = HotKeywordsConfig::getInstance()->option( $column_id );
			if ( empty( $hotKeyWord ) ) {
				continue;
			}
			$hotKeyWordArr = explode( ',', $hotKeyWord );
			$hotKeyWord    = array(
				'label'   => $hotKeyWordArr[0],
				'keyword' => $hotKeyWordArr[0],
			);
			if ( count( $hotKeyWordArr ) > 1 ) {
				$categoryIdList            = array_splice( $hotKeyWordArr, 1 );
				$hotKeyWord['categoryIds'] = array( $categoryIdList[0] );
			}
			$hotKeywords[] = $hotKeyWord;
		}

		return $hotKeywords;
	}
}