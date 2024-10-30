<?php
/*
 * Name: Product Comparison
 * Modules:
 * Module Types: PRODUCT
 */

use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;
wp_enqueue_style( 'moredeal-comparison-style', \Moredeal\PLUGIN_RES . '/css/block/block_comparison_style.css', false, Plugin::version() );
$data       = TemplateHelper::getComparisonData( TemplateHelper::markComparisonData( $data ?? array() ), $limit ?? null );
$post_id    = $post_id ?? get_the_ID();
$postType   = $postType ?? get_post_type( $post_id );
$postTitle  = $post_title ?? get_the_title( $post_id );
$template   = $template ?? 'block_comparison';
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
$params     = $params ?? array();
$isShowMore = $is_show_more ?? false;
$moreUrl    = AttributeHelper::getMoreUrl( $params, $template );

?>

<?php if ( $title ?? '' ): ?>
    <h3 class="moredeal-shortcode-title"><?php echo esc_html( $title ?? '' ); ?></h3>
<?php endif; ?>
<div class="comparison-template">
	<?php if ( ! empty( $data ) ): ?>
        <div class="products">
            <div class="comp_wrapper" data-global='<?php echo json_encode( $global ) ?>'>
				<?php foreach ( TemplateHelper::getComparisonRows() as $rowIndex => $row ): ?>
                    <div class="comp_row">
                        <div class="comp_thead"><?php echo esc_html( TemplateHelper::getComparisonRowText( $row ) ) ?></div>
						<?php TemplateHelper::displayPreview( $data, $row, $params ); ?>
						<?php TemplateHelper::displayTitle( $data, $row, $params ); ?>
						<?php TemplateHelper::displayRating( $data, $row, $params ); ?>
						<?php TemplateHelper::displayReview( $data, $row, $params ); ?>
						<?php TemplateHelper::displaySalesCount( $data, $row, $params ); ?>
						<?php TemplateHelper::displayPrice( $data, $row, $template, $params ); ?>
						<?php TemplateHelper::displayPrimeBenefits( $data, $row, $params ); ?>
						<?php TemplateHelper::displaySeeDeal( $data, $row, $template, $params ); ?>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
	<?php endif; ?>
</div>
<!-- 这里添加 more 按钮 -->
<?php TemplateHelper::displayMoreButton( $isShowMore, $moreUrl ) ?>
