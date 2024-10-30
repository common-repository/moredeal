<?php

namespace Moredeal\application;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\BlockTemplateManager;
use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TextHelper;

/**
 * 文章简码内容处理
 */
class BlockShortcode {

	const shortcode = 'moredeal-block';

	/**
	 * 文章标签内容处理
	 * @var BlockShortcode|null
	 */
	private static ?BlockShortcode $instance = null;

	/**
	 * 获取实例对象
	 * @return BlockShortcode|null
	 */
	public static function getInstance(): ?BlockShortcode {
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
		$template = array_key_exists('template', $atts) ? $atts['template'] : '';
		$template = BlockTemplateManager::getInstance()->prepareShortcodeTemplate( $template );
		$blockTemplateManager = BlockTemplateManager::getInstance();
		if ( empty( $template ) || ! $blockTemplateManager->isTemplateExists( $template ) ) {
			return $content;
		}
		// 准备简码属性
		$params = $this->prepareAttr( $atts );
		if ( empty( $params['post_id'] ) ) {
			global $post;
			$post_id = $post->ID;
		} else {
			$post_id = $params['post_id'];
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
			'modules'                 => null,
			'template'                => '',
			'post_id'                 => 0,
			'limit'                   => 0,
			'offset'                  => 0,
			'next'                    => 0,
			'title'                   => '',
			'cols'                    => 0,
			'sort'                    => '',
			'order'                   => '',
			'unit'                    => '',
			'groups'                  => '',
			'group'                   => '',
			'products'                => '',
			'product'                 => '',
			'hide'                    => '',
			'show'                    => '',
			'locale'                  => '',
			'add_query_arg'           => '',
			'btn_text'                => '',
			'btn_color'               => '',
			'price_color'             => '',
			'feature_sort_color'      => '',
			'feature_sort_text'       => '',
			'feature_number'          => 0,
			'compare_attrs'           => '',
			'best_choice_color'       => '',
			'best_choice_price_color' => '',
			'best_choice_text'        => '',
			'best_price_color'        => '',
			'best_price_price_color'  => '',
			'best_price_text'         => '',
			'grid_head_color'         => '',
			'grid_1_column_text'      => '',
			'grid_2_column_text'      => '',
			'grid_3_column_text'      => '',
			'grid_4_column_text'      => '',
			'grid_5_column_text'      => '',
			'grid_6_column_text'      => '',
			'grid_1_column_tip'       => '',
			'grid_2_column_tip'       => '',
			'grid_3_column_tip'       => '',
			'grid_4_column_tip'       => '',
			'grid_5_column_tip'       => '',
			'grid_6_column_tip'       => '',
			'search_template'         => '',
			'search_limit'            => '',
			'more'                    => '',
		);

		$allowed_atts  = apply_filters( 'moredeal_block_shortcode_atts', $allowed_atts );
		$params        = shortcode_atts( $allowed_atts, $atts );
		$allowed_sort  = array( 'price', 'discount', 'reverse' );
		$allowed_order = array( 'asc', 'desc' );

		$params['post_id']      = (int) $params['post_id'];
		$params['next']         = (int) $params['next'];
		$params['limit']        = (int) $params['limit'];
		$params['search_limit'] = (int) $params['search_limit'];
		$params['offset']       = (int) $params['offset'];
		$params['cols']         = (int) $params['cols'];
		$params['title']        = sanitize_text_field( $params['title'] );
		$params['unit']         = strtoupper( TextHelper::clear( $params['unit'] ) );
		$params['groups']       = sanitize_text_field( $params['groups'] );
		$params['group']        = sanitize_text_field( $params['group'] );
		//$params['hide'] = TemplateHelper::hideParamPrepare($params['hide']);
		$params['show']          = strtolower( TextHelper::clear( $params['show'] ) );
		$params['btn_text']      = wp_strip_all_tags( $params['btn_text'], true );
		$params['add_query_arg'] = sanitize_text_field( wp_strip_all_tags( $params['add_query_arg'], true ) );
		$params['locale']        = TextHelper::clear( $params['locale'] );

		if ( $params['group'] && ! $params['groups'] ) {
			$params['groups'] = $params['group'];
		}

		if ( $params['product'] && ! $params['products'] ) {
			$params['products'] = $params['product'];
		}

		if ( $params['add_query_arg'] ) {
			parse_str( $params['add_query_arg'], $params['add_query_arg'] );
		}


		$params['sort']  = strtolower( $params['sort'] );
		$params['order'] = strtolower( $params['order'] );

		if ( ! in_array( $params['sort'], $allowed_sort ) ) {
			$params['sort'] = '';
		}

		if ( ! in_array( $params['order'], $allowed_order ) ) {
			$params['order'] = '';
		}

		if ( $params['sort'] == 'discount' && ! $params['order'] ) {
			$params['order'] = 'desc';
		}

		if ( $params['modules'] ) {
			$modules    = explode( ',', $params['modules'] );
			$module_ids = array();
			foreach ( $modules as $key => $module_id ) {
				$module_id = trim( $module_id );
				//if (ModuleManager::getInstance()->isModuleActive($module_id))
				//    $module_ids[] = $module_id;
			}
			$params['modules'] = $module_ids;
		} else {
			$params['modules'] = array();
		}

		if ( $params['template'] ) {
			$params['template'] = BlockTemplateManager::getInstance()->prepareShortcodeTemplate( $params['template'] );
		}
		if ( in_array( 'more', array_values( $atts ) ) ) {
			$params['hasMore'] = true;
		} else {
			if ( array_key_exists( 'more', $atts ) ) {
				$params['hasMore'] = true;
			} else {
				$params['hasMore'] = false;
			}
		}

		$params = AttributeHelper::prepareAttributes( $params );

		return $params;
	}

}
