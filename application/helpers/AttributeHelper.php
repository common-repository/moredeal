<?php

namespace Moredeal\application\helpers;

use Moredeal\application\admin\GeneralConfig;
use Moredeal\application\admin\TemplateConfig;
use Moredeal\application\components\BlockSearchTemplateManager;
use Moredeal\application\components\BlockTemplateManager;

class AttributeHelper {

	/**
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	public static function prepareAttributes( $params ) {

		$params['template']                = self::getTemplate( $params['template'] );
		$params['limit']                   = self::getLimit( $params['limit'], $params['template'] );
		$params['btn_text']                = self::getButtonText( $params['btn_text'], $params['template'] );
		$params['btn_color']               = self::getBtnColor( $params['btn_color'], $params['template'] );
		$params['price_color']             = self::getPriceColor( $params['price_color'], $params['template'] );
		$params['feature_number']          = self::getFeatureNumber( $params['feature_number'], $params['template'] );
		$params['compare_attrs']           = self::getCompareAttrs( $params['compare_attrs'], $params['template'] );
		$params['feature_sort_color']      = self::getParam( $params['feature_sort_color'], 'card_feature_sort_color', '#e47911', $params['template'], array( 'block_card_feature' ) );
		$params['feature_sort_text']       = self::getParam( $params['feature_sort_text'], 'card_feature_sort_text', 'Bestseller No. ', $params['template'], array( 'block_card_feature' ) );
		$params['best_choice_color']       = self::getParam( $params['best_choice_color'], 'comparison_best_choice_color', '#256AAF', $params['template'], array( 'block_comparison' ) );
		$params['best_choice_price_color'] = self::getParam( $params['best_choice_price_color'], 'comparison_best_choice_price_color', '#256AAF', $params['template'], array( 'block_comparison' ) );
		$params['best_choice_text']        = self::getParam( $params['best_choice_text'], 'comparison_best_choice_text', 'Best Choice', $params['template'], array( 'block_comparison' ) );
		$params['best_price_color']        = self::getParam( $params['best_price_color'], 'comparison_best_price_color', '#2ABA9A', $params['template'], array( 'block_comparison' ) );
		$params['best_price_price_color']  = self::getParam( $params['best_price_price_color'], 'comparison_best_price_price_color', '#2ABA9A', $params['template'], array( 'block_comparison' ) );
		$params['best_price_text']         = self::getParam( $params['best_price_text'], 'comparison_best_price_text', 'Best Price', $params['template'], array( 'block_comparison' ) );
		$params['grid_head_color']         = self::getParam( $params['grid_head_color'], 'grid_head_color', '#55befb', $params['template'], array( 'block_grid' ) );
		$params['grid_1_column_text']      = self::getParam( $params['grid_1_column_text'], 'grid_1_column_text', 'Top Pick', $params['template'], array( 'block_grid' ) );
		$params['grid_2_column_text']      = self::getParam( $params['grid_2_column_text'], 'grid_2_column_text', 'Runner Up', $params['template'], array( 'block_grid' ) );
		$params['grid_3_column_text']      = self::getParam( $params['grid_3_column_text'], 'grid_3_column_text', 'We Also Like', $params['template'], array( 'block_grid' ) );
		$params['grid_4_column_text']      = self::getParam( $params['grid_4_column_text'], 'grid_4_column_text', 'Strong Contender', $params['template'], array( 'block_grid' ) );
		$params['grid_5_column_text']      = self::getParam( $params['grid_5_column_text'], 'grid_5_column_text', 'Fifth', $params['template'], array( 'block_grid' ) );
		$params['grid_6_column_text']      = self::getParam( $params['grid_6_column_text'], 'grid_6_column_text', 'Sixth', $params['template'], array( 'block_grid' ) );
		$params['grid_1_column_tip']       = self::getParam( $params['grid_1_column_tip'], 'grid_1_column_tip', 'Our top pick, The most recommended by us', $params['template'], array( 'block_grid' ) );
		$params['grid_2_column_tip']       = self::getParam( $params['grid_2_column_tip'], 'grid_2_column_tip', 'The second most recommended by us', $params['template'], array( 'block_grid' ) );
		$params['grid_3_column_tip']       = self::getParam( $params['grid_3_column_tip'], 'grid_3_column_tip', 'We also like this product', $params['template'], array( 'block_grid' ) );
		$params['grid_4_column_tip']       = self::getParam( $params['grid_4_column_tip'], 'grid_4_column_tip', 'A strong contender', $params['template'], array( 'block_grid' ) );
		$params['grid_5_column_tip']       = self::getParam( $params['grid_5_column_tip'], 'grid_5_column_tip', 'Fifth', $params['template'], array( 'block_grid' ) );
		$params['grid_6_column_tip']       = self::getParam( $params['grid_6_column_tip'], 'grid_6_column_tip', 'Sixth', $params['template'], array( 'block_grid' ) );
		$params['search_template']         = self::getSearchTemplate( $params['search_template'] );
		$params['search_limit']            = (int) $params['search_limit'];
		$params['more']                    = self::getMore( $params['more'] );

		return $params;
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public static function prepareSearchAttributes( $params ) {
		$params['template']              = BlockSearchTemplateManager::getInstance()->prepareShortcodeTemplate( 'render_block' );
		$params['search_page']           = self::getParam( $params['search_page'], 'search_page', '', $params['template'], array( 'block_search' ) );
		$params['search_template']       = self::getSearchTemplate( $params['search_template'] );
		$params['search_limit']          = (int) $params['search_limit'];
		$params['search_condition_type'] = self::getConditionType( $params['search_condition_type'] );
		$params['search_key']            = sanitize_text_field( $params['search_key'] );

		return $params;
	}

	/**
	 * 获取模版
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public static function getTemplate( string $template ): string {
		if ( $template ) {
			$template = BlockTemplateManager::getInstance()->prepareShortcodeTemplate( $template );
		}

		return $template;
	}

	/**
	 * 获取商品展示数量
	 *
	 * @param $limit
	 * @param $template
	 *
	 * @return int|null
	 */
	public static function getLimit( $limit, $template ): ?int {
		if ( $template == 'block_search' ) {
			return (int) $limit;
		}
		if ( $template == 'comparison' ) {
			if ( $limit ) {
				$limit = (int) $limit;
				if ( $limit < 2 || $limit > 5 ) {
					$limit = TemplateConfig::getInstance()->option( 'comparison_product_num' );
				}
			} else {
				$limit = TemplateConfig::getInstance()->option( 'comparison_product_num' );
			}

			return $limit;
		}

		if ( $template == 'grid' ) {
			if ( $limit ) {
				$limit = (int) $limit;
				if ( $limit < 3 || $limit > 6 ) {
					$limit = TemplateConfig::getInstance()->option( 'grid_product_num' );
				}
			} else {
				$limit = TemplateConfig::getInstance()->option( 'grid_product_num' );
			}

			return $limit;
		}

		if ( $limit ) {
			return (int) $limit;
		}

		return null;
	}

	/**
	 * 获取按钮文字
	 *
	 * @param $text
	 * @param $template
	 *
	 * @return string
	 */
	public static function getButtonText( $text, $template ): string {
		if ( $template != 'block_search' ) {

			if ( $text ) {
				$text = sanitize_text_field( $text );
			} else {
				$tem  = substr( $template, 6 );
				$text = TemplateConfig::getInstance()->option( $tem . '_btn_text' );
				if ( ! $text ) {
					$text = GeneralConfig::getInstance()->option( 'btn_text_buy_now' );
					if ( ! $text ) {
						$text = 'Buy Now';
					}
				}
			}

			return $text;
		}

		return $text;
	}

	/**
	 * 获取按钮颜色
	 *
	 * @param $color
	 * @param $template
	 *
	 * @return string
	 */
	public static function getBtnColor( $color, $template ): string {
		if ( $template != 'block_search' ) {
			if ( $color ) {
				$color = sanitize_text_field( $color );
			} else {
				$tem   = substr( $template, 6 );
				$color = TemplateConfig::getInstance()->option( $tem . '_button_color' );
				if ( ! $color ) {
					$color = GeneralConfig::getInstance()->option( 'button_color' );
					if ( ! $color ) {
						$color = '#d9534f';
					}
				}
			}

			return $color;
		}

		return $color;
	}

	/**
	 * 获取价格颜色
	 *
	 * @param $color
	 * @param $template
	 *
	 * @return string
	 */
	public static function getPriceColor( $color, $template ): string {
		if ( $template != 'block_search' && $template != 'block_top_listing' && $template != 'block_comparison' && $template != 'block_grid' ) {
			if ( $color ) {
				$color = sanitize_text_field( $color );
			} else {
				$tem   = substr( $template, 6 );
				$color = TemplateConfig::getInstance()->option( $tem . '_price_color' );
				if ( ! $color ) {
					$color = GeneralConfig::getInstance()->option( 'price_color' );
					if ( ! $color ) {
						$color = '#000000';
					}
				}
			}

			return $color;
		}

		return $color;
	}

	/**
	 * 获取需要展示商品的特性描述数量
	 *
	 * @param $number
	 *
	 * @return int|null
	 */
	private static function getFeatureNumber( $number, $template ): ?int {
		if ( $template == 'block_card_feature' ) {
			if ( $number ) {
				$number = (int) $number;
			} else {
				$number = TemplateConfig::getInstance()->option( 'card_feature_num' );
				if ( ! $number ) {
					$number = null;
				}
			}

			return $number;
		}

		return null;
	}

	/**
	 * 获取比较表格的比较属性
	 *
	 * @param $attr
	 * @param $template
	 *
	 * @return array
	 */
	private static function getCompareAttrs( $attr, $template ): array {
		if ( $template == 'block_comparison' ) {
			if ( $attr ) {
				$attrs = explode( ',', wp_strip_all_tags( $attr ) );
			} else {
				$attrs = TemplateConfig::getInstance()->option( 'comparison_row_attribute' );
				if ( ! $attrs ) {
					$attrs = array(
						'preview',
						'title',
						'rating',
						'review',
						'monthlySales',
						'price',
						'primeBenefits',
						'see'
					);
				}
			}

			return $attrs;
		}

		return array();
	}

	/**
	 * @param $attr
	 * @param $option
	 * @param $default
	 * @param $template
	 * @param $templates
	 *
	 * @return false|mixed|string
	 */
	public static function getParam( $attr, $option, $default, $template, $templates ) {
		if ( in_array( $template, $templates ) ) {
			if ( $attr ) {
				$attr = sanitize_text_field( $attr );
			} else {
				$attr = TemplateConfig::getInstance()->option( $option );
				if ( ! $attr ) {
					$attr = $default;
				}
			}

			return $attr;
		}

		return $attr;
	}

	/**
	 * @param $searchTemplate
	 *
	 * @return void
	 */
	private static function getSearchTemplate( $searchTemplate ) {
		if ( $searchTemplate ) {
			$searchTemplate = sanitize_text_field( $searchTemplate );
			if ( ! in_array( $searchTemplate, self::getCanRenderSearchTemplate() ) ) {
				$searchTemplate = TemplateConfig::getInstance()->option( 'search_template' );

				if ( ! $searchTemplate || ! in_array( $searchTemplate, self::getCanRenderSearchTemplate() ) ) {
					$searchTemplate = 'default';
				}
			}
		} else {
			$searchTemplate = TemplateConfig::getInstance()->option( 'search_template' );
			if ( ! $searchTemplate || ! in_array( $searchTemplate, self::getCanRenderSearchTemplate() ) ) {
				$searchTemplate = 'default';
			}
		}

		return $searchTemplate;
	}

	/**
	 * @param $attr
	 *
	 * @return array
	 */
	private static function getConditionType( $attr ): array {
		if ( $attr ) {
			$attr = explode( '|', wp_strip_all_tags( $attr ) );
		} else {
			$attr = array_values( TemplateConfig::getInstance()->option( 'search_condition_type' ) );
		}

		return $attr;
	}

	/**
	 * @param $more
	 *
	 * @return mixed|string
	 */
	public static function getKeyword( $more ) {
		if ( empty( $more ) ) {
			return '';
		}

		$keys = explode( '_', $more );
		if ( count( $keys ) >= 1 ) {
			return $keys[0];
		}

		return '';
	}

	/**
	 * @param $more
	 *
	 * @return array
	 */
	public static function getCategoryIds( $more ): array {
		if ( empty( $more ) ) {
			return array();
		}

		$keys = explode( '_', $more );
		if ( count( $keys ) > 1 ) {
			$categoryIdList = array_splice( $keys, 1 );
			if ( count( $categoryIdList ) > 0 ) {
				$categoryIds = array();
				foreach ( $categoryIdList as $categoryId ) {
					$categoryId = intval( $categoryId );
					if ( $categoryId > 0 ) {
						$categoryIds[] = $categoryId;
					}
				}

				return $categoryIds;
			}
		}

		return array();
	}

	/**
	 * @param $searchPage
	 * @param $template
	 *
	 * @return string
	 */
	public static function getSearchPage( $searchPage, $template ): string {
		if ( $template == 'block_search' ) {
			return '';
		}
		if ( empty( $searchPage ) ) {
			$searchPage = TemplateConfig::getInstance()->option( 'search_page' );
			if ( empty( $searchPage ) ) {
				$searchPage = '';
			}
		} else {
			$searchPage = sanitize_text_field( $searchPage );
		}

		return $searchPage;

	}

	/**
	 * @param array $params
	 * @param string $template
	 *
	 * @return string
	 */
	public static function getMoreUrl( array $params, string $template ): string {
		$hasMore = array_key_exists( 'hasMore', $params ) ? $params['hasMore'] : false;
		if ( ! $hasMore ) {
			return '';
		}
		$more           = array_key_exists( 'more', $params ) && $params['more'] ? $params['more'] : "";
		$searchTemplate = array_key_exists( 'search_template', $params ) && $params['search_template'] ? $params['search_template'] : '';
		if ( ! in_array( $searchTemplate, self::getCanRenderSearchTemplate() ) ) {
			$searchTemplate = '';
		}
		$searchTemplate = BlockTemplateManager::getInstance()->prepareShortcodeTemplate( $searchTemplate );
		$keyword        = self::getKeyword( $more );
		$categoryIds    = self::getCategoryIds( $more );
		$searchPage     = self::getSearchPage( array_key_exists( 'search_page', $params ) && $params['search_page'] ? $params['search_page'] : "", $template );
		$searchLimit    = self::getSearchLimit( $params );
		if ( $searchPage ) {
			$moreUrl = $searchPage;
			if ( $searchTemplate ) {
				$moreUrl = add_query_arg( 'searchTemplate', substr( $searchTemplate, 6 ), $moreUrl );
			}
			if ( $searchLimit ) {
				$moreUrl = add_query_arg( 'searchLimit', $searchLimit, $moreUrl );
			}
			if ( $keyword ) {
				$moreUrl = add_query_arg( 'keyword', $keyword, $moreUrl );
			}
			if ( count( $categoryIds ) > 0 ) {
				$moreUrl = add_query_arg( 'categoryIds', $categoryIds[0], $moreUrl );
			}
		} else {
			$moreUrl = '';
		}

		return $moreUrl;
	}

	/**
	 *
	 * @param $params
	 *
	 * @return int|null
	 */
	public static function getSearchLimit( $params ): ?int {
		$searchLimit = array_key_exists( 'search_limit', $params ) && $params['search_limit'] ? $params['search_limit'] : null;
		if ( $searchLimit ) {
			$searchLimit = intval( $searchLimit );
			if ( $searchLimit <= 0 ) {
				$searchLimit = null;
			}
			if ( $searchLimit > 20 ) {
				$searchLimit = 20;
			}
		}

		return $searchLimit;
	}

	/**
	 * @return string[]
	 */
	public static function getCanRenderSearchTemplate(): array {
		return array(
			'default',
			'block_default',
			'card_feature',
			'block_card_feature',
			'item',
			'block_item',
			'offers_list',
			'block_offers_list',
			'top_listing',
			'block_top_listing',
		);
	}

	/**
	 * @param $more
	 *
	 * @return string
	 */
	private static function getMore( $more ): string {
		if ( empty( $more ) ) {
			$more = '';
		}

		return sanitize_text_field( $more );
	}
}