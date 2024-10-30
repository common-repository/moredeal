<?php

namespace Moredeal\application;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\AttributeHelper;

/**
 * 文章简码内容处理
 */
class BlockSearchShortcode {

	const shortcode = 'moredeal-search';

	/**
	 * 文章标签内容处理
	 * @var BlockSearchShortcode|null
	 */
	private static ?BlockSearchShortcode $instance = null;

	/**
	 * 获取实例对象
	 * @return BlockSearchShortcode|null
	 */
	public static function getInstance(): ?BlockSearchShortcode {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
		// 添加Block简码
		add_shortcode( self::shortcode, array( $this, 'viewData' ) );
	}

	/**
	 * 简码内容处理
	 *
	 * @param $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function viewData( $atts, string $content = "" ): string {
		// 准备简码属性
		$params = $this->prepareAttr( $atts );
		if ( empty( $params['post_id'] ) ) {
			global $post;
			$post_id = $post->ID;
		} else {
			$post_id = array_key_exists( 'post_id', $params ) ? $params['post_id'] : 0;
		}
		// 渲染商品数据
		return ModuleViewer::getInstance()->viewBlockData( $post_id, $params, $content );

	}

	/**
	 * 准备简码属性
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	private function prepareAttr( $atts ): array {
		$allowed_atts = array(
			'search_template'         => '',
			'search_page'             => '',
			'search_limit'            => '',
			'search_condition_type'   => '',
			'search_key'              => '',
		);

		$allowed_atts  = apply_filters( 'moredeal_block_shortcode_atts', $allowed_atts );
		$params        = shortcode_atts( $allowed_atts, $atts );
		return AttributeHelper::prepareSearchAttributes( $params );
	}

}
