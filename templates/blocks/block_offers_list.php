<?php
/*
 * Name: Sorted offers order by price
 * Modules:
 * Module Types: PRODUCT
 *
 */

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;

$data       = TemplateHelper::getData( $data ?? array(), $limit ?? null );
$data       = TemplateHelper::sortByPrice( $data ?? array(), $order ?? 'asc', $sort ?? 'price' );
$post_id    = $post_id ?? get_the_ID();
$postType   = $postType ?? get_post_type( $post_id );
$postTitle  = $post_title ?? get_the_title( $post_id );
$template   = $template ?? 'block_offers_list';
$global     = array(
	'post_id'          => $post_id,
	'post_title'       => $postTitle,
	'template'         => $template,
	'auth_code'        => empty( $auth_code ) ? '' : $auth_code,
	'isPro'            => empty( $isPro ) ? false : $isPro,
	'moredeal_version' => empty( $moredeal_version ) ? '' : $moredeal_version,
	'wp_version'       => empty( $wp_version ) ? '' : $wp_version,
	'wp_addr'          => empty( $wp_addr ) ? '' : $wp_addr,
);
$btnText    = empty( $btn_text ) ? '' : $btn_text;
$btnColor   = empty( $btn_color ) ? '' : $btn_color;
$params     = $params ?? array();
$priceColor = array_key_exists( 'price_color', $params ) ? $params['price_color'] : '';
$isShowMore = $is_show_more ?? false;
$moreUrl    = AttributeHelper::getMoreUrl( $params, $template );

?>

<div class="moredeal-container moredeal-list-withlogos">
	<?php if ( $title ?? '' ): ?>
        <h3><?php echo esc_html( $title ?? '' ); ?></h3>
	<?php endif; ?>
    <div class="moredeal-listcontainer">
		<?php if ( ! empty( $data ) ): ?>
        <div class="offer-template" <?php TemplateHelper::templateGlobalStyle( $isShowMore, $moreUrl, $postType ); ?>
             data-global='<?php echo json_encode( $global ) ?>'>
		    <?php foreach ( $data as $key => $item ): ?>
			    <?php $it = array(
				    'post_id'         => $item['post_id'],
				    'product_code'    => $item['code'],
				    'category_id'     => $item['category_id'],
				    's_id'            => $item['s_id'],
				    'trace_id'        => $item['trace_id'],
				    'view_id'         => $item['view_idx'],
				    'search_location' => $item['search_location'],
			    ) ?>
                <div class="offer-list-row" data-product='<?php echo json_encode( $it ) ?>'>
				    <?php $this->renderBlock( 'list_row', array(
					    'item'       => (array) $item,
					    'template'   => $template,
					    'params'     => $params,
					    'btnText'    => $btnText,
					    'btnColor'   => $btnColor,
					    'priceColor' => $priceColor,
				    ) ); ?>
                </div>
		    <?php endforeach; ?>
		    <?php endif; ?>
        </div>
    </div>
    <!-- 这里添加 more 按钮 -->
	<?php TemplateHelper::displayMoreButton( $isShowMore, $moreUrl ) ?>
</div>
