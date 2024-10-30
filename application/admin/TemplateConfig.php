<?php

namespace Moredeal\application\admin;

use Moredeal\application\components\Config;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;

class TemplateConfig extends Config {

	const slug = "moredeal-template-config";

	public function add_admin_menu() {
		add_submenu_page( Plugin::slug, __( 'Templates', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'Templates', 'moredeal' ), 'manage_options', $this->page_slug, array(
			$this,
			'templatesIndex'
		) );
	}

	/**
	 * 加载 template 页面
	 * @return void
	 */
	public function templatesIndex(): void {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'moredeal-admin-ui-css', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version );
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css', false, Plugin::version );
		AdminPlugin::render( 'template_index', array( 'page_slug' => $this->page_slug() ) );
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
		return "moredeal_template_options";
	}

	/**
	 * 配置选项
	 * @return array[]
	 */
	protected function options(): array {
		$options = array(
			// 按钮颜色
			'item_button_color'            => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Card', 'moredeal' ),
			),
			// 价格颜色
			'item_price_color'             => array(
				'title'       => __( 'Price color', 'moredeal' ),
				'description' => __( 'Price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Card', 'moredeal' ),
			),
			// 按钮 text
			'item_btn_text'                => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Card', 'moredeal' ),
			),

			// 按钮颜色
			'offers_list_button_color'     => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Order By Price', 'moredeal' ),
			),
			// 价格颜色
			'offers_list_price_color'      => array(
				'title'       => __( 'Price color', 'moredeal' ),
				'description' => __( 'Price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Order By Price', 'moredeal' ),
			),
			// 按钮 text
			'offers_list_btn_text'         => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Order By Price', 'moredeal' ),
			),


			// 按钮颜色
			'top_listing_button_color'     => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Top Listing', 'moredeal' ),
			),
			// 按钮 text
			'top_listing_btn_text'         => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Top Listing', 'moredeal' ),
			),


			// 按钮颜色
			'card_feature_button_color'    => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Card Feature', 'moredeal' ),
			),
			// 价格颜色
			'card_feature_price_color'     => array(
				'title'       => __( 'Price color', 'moredeal' ),
				'description' => __( 'Price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Card Feature', 'moredeal' ),
			),
			// 按钮 text
			'card_feature_btn_text'        => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Card Feature', 'moredeal' ),
			),
			// 价格颜色
			'card_feature_sort_color'     => array(
				'title'       => __( 'Sort color', 'moredeal' ),
				'description' => __( 'The sort column background color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#e47911',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Card Feature', 'moredeal' ),
			),
			// 按钮 text
			'card_feature_sort_text'        => array(
				'title'       => __( 'Sort text', 'moredeal' ),
				'description' => __( 'Sort text for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 'Bestseller No. ',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Card Feature', 'moredeal' ),
			),
			// 特性数量
			'card_feature_num'             => array(
				'title'       => __( 'Feature Number', 'moredeal' ),
				'description' => __( 'The number of product feature to display, default show all feature', 'moredeal' ),
				'callback'    => array( $this, 'render_number' ),
				'validator'   => array(
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'numberValidator2' ),
						'message' => __( 'The number is must greater than or equal to 0', 'moredeal' ),
					),
				),
				'style'       => 'width: 91px',
				'section'     => __( 'Card Feature', 'moredeal' ),
			),


			// 按钮颜色
			'comparison_button_color'      => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// 按钮 text
			'comparison_btn_text'          => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// 商品数量
			'comparison_product_num'       => array(
				'title'       => __( 'Product Number', 'moredeal' ),
				'description' => __( 'The number of items used for comparison, The product proficiency must be greater than or equal to 2 and less than or equal to 5', 'moredeal' ),
				'callback'    => array( $this, 'render_number' ),
				'validator'   => array(
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'numberValidator' ),
						'message' => __( 'The product proficiency must be greater than or equal to 2 and less than or equal to 5', 'moredeal' ),
					),
				),
				'style'       => 'width: 91px',
				'default'     => 3,
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// 需要展示的属性
			'comparison_row_attribute'     => array(
				'title'            => __( 'Product attribute display', 'moredeal' ),
				'description'      => __( 'The product attributes that need to be compared are displayed on the page.', 'moredeal' ),
				'checkbox_options' => self::getComparisonRows(),
				'callback'         => array( $this, 'render_checkbox_list' ),
				'default'          => array(
					'preview',
					'title',
					'rating',
					'review',
					'monthlySales',
					'price',
					'primeBenefits',
					'see'
				),
				'section'          => __( 'Product Comparison', 'moredeal' ),
			),
			// best_choice_color
			'comparison_best_choice_color' => array(
				'title'       => __( 'Best Choice color', 'moredeal' ),
				'description' => __( 'The best choice product column background color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#256AAF',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// 价格颜色
			'comparison_best_choice_price_color'       => array(
				'title'       => __( 'Best Choice price color', 'moredeal' ),
				'description' => __( 'Best Choice price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#256AAF',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// best text
			'comparison_best_choice_text'  => array(
				'title'       => __( 'Best choice text', 'moredeal' ),
				'description' => __( 'The best choice product text', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 'Best Choice',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// best_price_color
			'comparison_best_price_color'  => array(
				'title'       => __( 'Best price color', 'moredeal' ),
				'description' => __( 'The best price product column background color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#2ABA9A',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// 价格颜色
			'comparison_best_price_price_color'       => array(
				'title'       => __( 'Best price price color', 'moredeal' ),
				'description' => __( 'Best price price color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#2ABA9A',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),
			// best_price_text
			'comparison_best_price_text'   => array(
				'title'       => __( 'Best price Text', 'moredeal' ),
				'description' => __( 'The best price product text', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 'Best Price',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Comparison', 'moredeal' ),
			),


			// 按钮颜色
			'grid_button_color'            => array(
				'title'       => __( 'Button color', 'moredeal' ),
				'description' => __( 'Button color for default templates.', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'default'     => '',
				'style'       => 'width: 150px',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Grid', 'moredeal' ),
			),
			// 按钮 text
			'grid_btn_text'                => array(
				'title'       => __( 'Button text', 'moredeal' ),
				'description' => sprintf( __( 'It will be used instead of %s.', 'moredeal' ), __( 'Buy Now', 'moredeal' ) ) . ' ' . __( 'You can use tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Grid', 'moredeal' ),
			),
			// 颜色
			'grid_head_color'              => array(
				'title'       => __( 'Table head color', 'moredeal' ),
				'description' => __( 'Table head color for product grid', 'moredeal' ),
				'callback'    => array( $this, 'render_color_picker' ),
				'style'       => 'width: 150px',
				'default'     => '#55befb',
				'validator'   => array(
					'trim',
				),
				'section'     => __( 'Product Grid', 'moredeal' ),
			),
			// 商品数量
			'grid_product_num'             => array(
				'title'       => __( 'Product Number', 'moredeal' ),
				'description' => __( 'The number of product tp display, The product proficiency must be greater than or equal to 3 and less than or equal to 6', 'moredeal' ),
				'callback'    => array( $this, 'render_number' ),
				'validator'   => array(
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'numberValidator3' ),
						'message' => __( 'The product proficiency must be greater than or equal to 3 and less than or equal to 6', 'moredeal' ),
					),
				),
				'style'       => 'width: 91px',
				'default'     => 4,
				'section'     => __( 'Product Grid', 'moredeal' ),
			),
		);
		foreach ( self::getColumn() as $column_id => $column_name ) {
			$options[ $column_id ] = array(
				'title'       => __( $column_name, 'moredeal' ),
				'description' => __( $column_name, 'moredeal' ) . __( 'product header title of', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => __( self::getDefaultColumnText( $column_id ), 'moredeal' ),
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Grid', 'moredeal' ),
				'metaboxInit' => true,
			);
		}

		foreach ( self::getTips() as $column_id => $tip_name ) {
			$options[ $column_id ] = array(
				'title'       => __( $tip_name, 'moredeal' ),
				'description' => __( $tip_name, 'moredeal' ) . __( 'product header title tip of', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'style'       => 'width: 500px',
				'validator'   => array(
					'strip_tags',
				),
				'section'     => __( 'Product Grid', 'moredeal' ),
				'metaboxInit' => true,
			);
		}

		$options['search_page'] = array(
			'title'       => __( 'Search Page', 'moredeal' ),
			'description' => __( 'The template click more button will be jump this page', 'moredeal' ),
			'callback'    => array( $this, 'render_input' ),
			'style'       => 'width: 500px',
			'default'     => '',
			'section'     => __( 'Search Product', 'moredeal' ),
			'metaboxInit' => true,
		);
		$options['search_template'] = array(
			'title'       => __( 'Search template', 'moredeal' ),
			'description' => __( 'The search products will be display in this template, you can enter default, offers_list, top_listing, item, card_feature', 'moredeal' ),
			'callback'    => array( $this, 'render_input' ),
			'style'       => 'width: 500px',
			'default'     => 'default',
			'section'     => __( 'Search Product', 'moredeal' ),
			'metaboxInit' => true,
		);
		// 需要展示的属性
		$options['search_condition_type'] = array(
			'title'            => __( 'Search Condition Type Display', 'moredeal' ),
			'description'      => __( 'The search condition Type will be display in this template', 'moredeal' ),
			'checkbox_options' => array(
				'hotKeywords'         => __( 'Hot Keywords', 'moredeal' ),
				'selectionStrategy'   => __( 'Selection Strategy', 'moredeal' ),
				'selectionConditions' => __( 'Selection Conditions', 'moredeal' ),
			),
			'callback'         => array( $this, 'render_checkbox_list' ),
			'default'          => array(
				'hotKeywords',
				'selectionStrategy',
				'selectionConditions',
			),
			'section'          => __( 'Search Product', 'moredeal' ),
		);

		return $options;
	}

	public static function getComparisonRows(): array {
		return array(
			'preview'       => 'Preview',
			'title'         => 'Title',
			'rating'        => 'Rating',
			'review'        => 'Review',
			'monthlySales'  => 'Monthly sales',
			'price'         => 'Price',
			'primeBenefits' => 'Prime Benefits',
			'see'           => 'See Deal',
		);
	}

	public static function getColumn(): array {
		return array(
			'grid_1_column_text' => 'First column Text',
			'grid_2_column_text' => 'Second column Text',
			'grid_3_column_text' => 'Third column Text',
			'grid_4_column_text' => 'Fourth column Text',
			'grid_5_column_text' => 'Fifth column Text',
			'grid_6_column_text' => 'Sixth column Text',
		);
	}

	public static function getDefaultColumnText( $column_id ): string {
		$defaultColumnText = array(
			'grid_1_column_text' => 'Top Pick',
			'grid_2_column_text' => 'Runner Up',
			'grid_3_column_text' => 'We Also Like',
			'grid_4_column_text' => 'Strong Contender',
			'grid_5_column_text' => 'Fifth',
			'grid_6_column_text' => 'Sixth',
		);

		return $defaultColumnText[ $column_id ];
	}

	public static function getTips(): array {
		return array(
			'grid_1_column_tip' => 'First column Tip',
			'grid_2_column_tip' => 'Second column Tip',
			'grid_3_column_tip' => 'Third column Tip',
			'grid_4_column_tip' => 'Fourth column Tip',
			'grid_5_column_tip' => 'Fifth column Tip',
			'grid_6_column_tip' => 'Sixth column Tip',
		);
	}

}