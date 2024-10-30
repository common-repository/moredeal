<?php
/*
 * Name: Product Grid
 * Modules:
 * Module Types: PRODUCT
 */

use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;
wp_enqueue_style( 'moredeal-grid-style', \Moredeal\PLUGIN_RES . '/css/block/block_grid_style.css', false, Plugin::version() );
$data       = TemplateHelper::getLimitData( $data ?? array(), $limit ?? null );
$ratings    = TemplateHelper::generateStaticRatings( count( $data ) );
$post_id    = $post_id ?? get_the_ID();
$postType   = $postType ?? get_post_type( $post_id );
$postTitle  = $post_title ?? get_the_title( $post_id );
$template   = $template ?? 'block_grid';
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
$isShowMore = $is_show_more ?? false;
$moreUrl    = AttributeHelper::getMoreUrl( $params, $template );

?>

<?php if ( $title ?? '' ): ?>
    <h3 class="moredeal-shortcode-title"><?php echo esc_html( $title ?? '' ); ?></h3>
<?php endif; ?>
<div class="grid-template">
    <div class="overall-row" data-global='<?php echo json_encode( $global ) ?>'>
		<?php if ( ! empty( $data ) ): ?>
			<?php foreach ( $data as $index => $item ): ?>
				<?php $it = array(
					'post_id'         => $item['post_id'],
					'product_code'    => $item['code'],
					'category_id'     => $item['category_id'],
					's_id'            => $item['s_id'],
					'trace_id'        => $item['trace_id'],
					'view_id'         => $item['view_idx'],
					'search_location' => $item['search_location'],
				) ?>
                <div class="moredeal-item" data-product='<?php echo json_encode( $it ) ?>'>
                    <div class="products">
                        <div class="card-section">
                            <div class="overall-title"
                                 style="background-color: <?php echo TemplateHelper::getHeaderColumnColor( $params ) ?>">
								<?php echo TemplateHelper::getColumnText( $index, $params ); ?>
                            </div>
                            <div class="card-body">
                                <div class="card-score">
									<?php echo $ratings[ $index ]; ?>
                                    <span class="icons"><?php TemplateHelper::printColumnTip( $index, $params ); ?></span>
                                    <div class="melt-score">MDC Score</div>
                                </div>
								<?php if ( $item['img'] ): ?>
                                    <div class="card-image">
                                        <a<?php TemplateHelper::printRel(); ?> target="_blank"
                                                                               href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
											<?php TemplateHelper::displayImageWidth( $item, 150, 150, 150 ); ?>
                                        </a>
                                    </div>
								<?php endif; ?>
                                <div class="productNameContainer">
									<?php if ( $item['title'] ): ?>
                                        <div class="overview-productName">
                                            <a class="product__title product-titles"
                                               data-title="<?php echo TemplateHelper::printTitle( $item, 100000 ) ?>" <?php TemplateHelper::printRel(); ?>
                                               target="_blank"
                                               href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
												<?php echo TemplateHelper::printTitle( $item, 50 ) ?>
                                            </a>
                                        </div>
									<?php endif; ?>
                                </div>
                                <div class="top-price-link">
                                    <a class="btn-price-grid" <?php TemplateHelper::printRel(); ?>
                                       target="_blank"
                                       href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>"
                                       style="background-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>;
                                               border-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>">
										<?php TemplateHelper::buyNowBtnText( true, $item, $btnText, $template ); ?>
                                    </a>
                                    <div class="store-logo">
                                        <a <?php TemplateHelper::printRel(); ?> target="_blank"
                                                                                href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
                                            <img src="<?php echo \Moredeal\PLUGIN_RES . '/logos/amazon.png' ?>" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
		<?php endif; ?>
    </div>
</div>
<!-- 这里添加 more 按钮 -->
<?php TemplateHelper::displayMoreButton( $isShowMore, $moreUrl ) ?>
