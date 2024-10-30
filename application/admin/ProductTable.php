<?php

namespace Moredeal\application\admin;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\ListTable;
use Moredeal\application\components\ModuleManager;
use Moredeal\application\components\MoredealManager;
use Moredeal\application\components\MoredealProduct;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\helpers\TextHelper;
use Moredeal\application\models\ProductModel;
use Moredeal\application\Plugin;

/**
 * ProductTable class file
 */
class ProductTable extends ListTable {

	const per_page = 15;

	/**
	 * 表格列
	 * @return array
	 */
	function get_columns(): array {
		return array(
			'img'          => '',
			'title'        => ProductModel::model()->getAttributeLabel( 'title' ),
			'module_id'    => __( 'Module', 'moredeal' ),
			'stock_status' => ProductModel::model()->getAttributeLabel( 'stock_status' ),
			'price'        => ProductModel::model()->getAttributeLabel( 'price' ),
			'last_update'  => ProductModel::model()->getAttributeLabel( 'last_update' ),
		);
	}

	/**
	 * 可排序的列
	 * @return array
	 */
	function get_sortable_columns(): array {
		return array(
			'price'        => array( 'price', true ),
			'title'        => array( 'title', true ),
			'module_id'    => array( 'module_id', true ),
			'stock_status' => array( 'stock_status', true ),
			'last_update'  => array( 'last_update', true ),
		);
	}

	/**
	 * get_bulk_actions
	 * @return array
	 */
	function get_bulk_actions(): array {
		return array();
	}

	/**
	 * 额外的搜索条件
	 *
	 * @param $which
	 *
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
		if ( $which != 'top' ) {
			return;
		}
		echo '<div class="alignleft actions">';
		$this->print_modules_dropdown();
		submit_button( __( 'Filter', 'moredeal' ), '', 'filter_action', false, array( 'id' => 'product-query-submit' ) );

		echo '</div>';
	}

	/**
	 * 根据模块筛选下拉框
	 * @return void
	 */
	private function print_modules_dropdown() {
//		$modules            = ModuleManager::getInstance()->getAffiliteModulesList( true );
		$modules = array("Amazon" => "Amazon");
		$statuses            = ProductModel::getStockStatuses();
		$selected_module_id = ! empty( $_GET['module_id'] ) ? TextHelper::clear( sanitize_text_field( wp_unslash( $_GET['module_id'] ) ) ) : '';
		$selected_stock = ! empty( $_GET['stock_status'] ) ? TextHelper::clear( sanitize_text_field( wp_unslash( $_GET['stock_status'] ) ) ) : '';

		echo '<select name="module_id" id="dropdown_module_id"><option value="">' . esc_html__( 'Filter by module', 'moredeal' ) . '</option>';
		foreach ( $modules as $module_id => $module_name ) {
			echo '<option ' . selected( $module_id, $selected_module_id, false ) . ' value="' . esc_attr( $module_id ) . '">' . esc_html( $module_name ) . '</option>';
		}
		echo '</select>';
		echo '<select name="stock_status" id="dropdown_stock_id"><option value="">' . esc_html__( 'Filter by Stock', 'moredeal' ) . '</option>';
		foreach ( $statuses as $stock => $index ) {
			echo '<option ' . selected( $stock, $selected_stock, false ) . ' value="' . esc_attr( $stock ) . '">' . esc_html( $index ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * 组装查询条件
	 * @return string
	 */
	protected function getWhereFilters(): string {
		global $wpdb;
		$where = '';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$s = trim( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) );
			if ( $where ) {
				$where .= ' AND ';
			}

			if ( is_numeric( $s ) ) {
				$where .= 'post_id = ' . (int) $s;
			} else {
				$where .= $wpdb->prepare( 'title LIKE %s', '%' . $wpdb->esc_like( sanitize_text_field( $s ) ) . '%' );
			}
		}

		// filters
		if ( isset( $_GET['stock_status'] ) && $_GET['stock_status'] !== '' && $_GET['stock_status'] !== 'all' ) {
			$stock_status = (int) $_GET['stock_status'];

			if ( array_key_exists( $stock_status, ProductModel::getStockStatuses() ) ) {
				if ( $where ) {
					$where .= ' AND ';
				}

				$where .= $wpdb->prepare( 'stock_status = %d', $stock_status );
			}
		}

		if ( isset( $_GET['module_id'] ) && $_GET['module_id'] !== '' ) {
			$module_id = TextHelper::clear( sanitize_text_field( wp_unslash( $_GET['module_id'] ) ) );
			if ( ModuleManager::getInstance()->moduleExists( $module_id ) ) {
				if ( $where ) {
					$where .= ' AND ';
				}
				$where .= $wpdb->prepare( 'module_id = %s', $module_id );
			}
		}

		return $where;
	}

	/**
	 * views
	 * @return array
	 */
	protected function get_views(): array {
		$status_links = array();
//		$class        = ( ! isset( $_REQUEST['stock_status'] ) || $_REQUEST['stock_status'] === '' || $_REQUEST['stock_status'] === 'all' ) ? ' class="current"' : '';
//		$admin_url    = get_admin_url( get_current_blog_id(), 'admin.php?page='.ProductController::slug );
//
//		$statuses            = ProductModel::getStockStatuses();
//		$total               = ProductModel::model()->count();
//		$status_links['all'] = '<a href="' . $admin_url . '&stock_status=all"' . $class . '>' . __( 'All', 'moredeal' ) . sprintf( ' <span class="count">(%s)</span></a>', number_format_i18n( $total ) );
//		foreach ( $statuses as $status_id => $status_name ) {
//			$total                      = ProductModel::model()->count( 'stock_status = ' . (int) $status_id );
//			$class                      = ( isset( $_REQUEST['stock_status'] ) && $_REQUEST['stock_status'] !== '' && sanitize_text_field( wp_unslash( $_REQUEST['stock_status'] ) ) == $status_id ) ? ' class="current"' : '';
//			$status_links[ $status_id ] = '<a href="' . $admin_url . '&stock_status=' . (int) $status_id . '"' . $class . '>' . esc_html( $status_name );
//			$status_links[ $status_id ] .= sprintf( ' <span class="count">(%s)</span></a>', number_format_i18n( $total ) );
//		}

		return $status_links;
	}

	/**
	 * 图片列
	 * @param $item
	 *
	 * @return void
	 */
	function column_img( $item ) {
		if ( $item['post_id'] ) {
			echo '
			<a href="' . esc_url( get_edit_post_link( $item['post_id'] ) ) . '">
				<img class="attachment-thumbnail size-thumbnail wp-post-image" src="' . esc_url( $item['img'] ) . '" width="60", height="60" />
			</a>';
		}
	}

	/**
	 * 标题列
	 * @param $item
	 *
	 * @return void
	 */
	function column_title( $item ): string {
		if ( ! trim( $item['title'] ) ) {
			$title = __( '(no title)', 'moredeal' );
		} else {
			$title = TextHelper::truncate( $item['title'], 80 );
		}

		if($item['post_id']) {
			$edit_link = get_edit_post_link( $item['post_id'] ) . '#' . $item['module_id'] . '-' . $item['code'];
			$actions   = array(
				'post_id' => sprintf( __( 'Post ID: %d', 'moredeal' ), $item['post_id'] ),
				'view'    => sprintf( '<a href="%s">%s</a>', get_post_permalink( $item['post_id'] ), __( 'View', 'moredeal' ) ),
				'edit' => sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), __( 'Edit', 'moredeal' ) ),
			);
		}
		if ( $item['url'] ) {
			$actions['goto'] = sprintf( '<a target="_blank" href="%s">%s</a>', esc_url(MoredealManager::getAssociateTagUrl($item['url'], $item['module_id'] )), __( 'Go to', 'moredeal' ) );
		}

		return '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a></strong>' .
		       $this->row_actions( $actions );
	}

	/**
	 * 价格列
	 * @param $item
	 *
	 * @return string
	 */
	function column_price( $item ): string {
		$res = (float) $item['price_old'] ? '<del>' . wp_kses_post( TemplateHelper::formatPriceCurrency( $item['price_old'], $item['currency_code'] ) ) . '</del>' : '';
		$res .= (float) $item['price'] ? '<ins>' . wp_kses_post( TemplateHelper::formatPriceCurrency( $item['price'], $item['currency_code'] ) ) . '</ins>' : '<span class="na">&ndash;</span>';

		return $res;
	}

	/**
	 * 库存列
	 * @param $item
	 *
	 * @return string|void
	 */
	function column_stock_status( $item ) {
		if ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_IN_STOCK ) {
			return '<mark class="instock">' . __( 'In stock', 'moredeal' ) . '</mark>';
		} elseif ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_OUT_OF_STOCK ) {
			return '<mark class="outofstock">' . __( 'Out of stock', 'moredeal' ) . '</mark>';
		} elseif ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_UNKNOWN ) {
			return '<span class="na">&ndash;</span>';
		}
	}

	/**
	 * Module列
	 * @param $item
	 *
	 * @return string|void
	 * @throws \Exception
	 */
	function column_module_id( $item ) {
		$module_id = $item['module_id'];
		if ( ! ModuleManager::getInstance()->moduleExists( $module_id ) ) {
			return;
		}
		$module = ModuleManager::getInstance()->factory( $item['module_id'] );
		$output = '<strong>' . esc_html( $module->getName() ) . '</strong>';

		if ( ! $module->isActive() ) {
			$output .= '<br><mark class="inactive">' . esc_html( __( 'inactive', 'moredeal' ) ) . '</mark>';
		}

		return $output;
	}

	/**
	 * 日期列
	 * @param $item
	 *
	 * @return string
	 */
	function column_last_update( $item ): string {
		if ( empty( $item['last_update'] ) ) {
			return '<span class="na">&ndash;</span>';
		}

		$last_update_timestamp = strtotime( $item['last_update'] );
		$show_date_time        = TemplateHelper::dateFormatFromGmt( $item['last_update'], true );

		// last 24 hours?
		if ( $last_update_timestamp > strtotime( '-1 day', current_time( 'timestamp', true ) ) ) {
			$show_date = sprintf(
				__( '%s ago', '%s = human-readable time difference', 'moredeal' ), human_time_diff( $last_update_timestamp, current_time( 'timestamp', true ) )
			);
		} else {
			$show_date = TemplateHelper::dateFormatFromGmt( $item['last_update'], false );
		}

		return sprintf(
			'<abbr datetime="%1$s" title="%2$s">%3$s</abbr>', esc_attr( $show_date_time ), esc_attr( $show_date_time ), esc_html( $show_date )
		);
	}

}
