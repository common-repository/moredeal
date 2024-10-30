<?php
/*
 * Name: Product card feature
 * Modules:
 * Module Types: PRODUCT
 */

use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;
wp_enqueue_style( 'moredeal-card-feature-style', \Moredeal\PLUGIN_RES . '/css/block/block_card_feature_style.css', false, Plugin::version() );
$data     = TemplateHelper::getData( $data ?? array(), $limit ?? null );
$post_id  = $post_id ?? get_the_ID();
$postType = $postType ?? get_post_type( $post_id );
$postTitle = $post_title ?? get_the_title( $post_id );
$template = $template ?? 'block_card_feature';
$global   = array(
	'post_id'          => $post_id,
	'post_title'       => $postTitle,
	'template'         => $template,
	'auth_code'        => empty( $auth_code ) ? '' : $auth_code,
	'isPro'            => empty( $isPro ) ? false : $isPro,
	'moredeal_version' => empty( $moredeal_version ) ? '' : $moredeal_version,
	'wp_version'       => empty( $wp_version ) ? '' : $wp_version,
	'wp_addr'          => empty( $wp_addr ) ? '' : $wp_addr,
);

$params        = $params ?? array();
$isShowMore    = $is_show_more ?? false;
$btnText       = empty( $btn_text ) ? '' : $btn_text;
$btnColor      = empty( $btn_color ) ? '' : $btn_color;
$priceColor    = array_key_exists( 'price_color', $params ) && ! empty( $params['price_color'] ) ? $params['price_color'] : '';
$sortText      = array_key_exists( 'feature_sort_text', $params ) && ! empty( $params['feature_sort_text'] ) ? $params['feature_sort_text'] : '';
$sortColor     = array_key_exists( 'feature_sort_color', $params ) && ! empty( $params['feature_sort_color'] ) ? $params['feature_sort_color'] : '';
$featureNumber = array_key_exists( 'feature_number', $params ) && ! empty( $params['feature_number'] ) ? $params['feature_number'] : null;
$moreUrl       = AttributeHelper::getMoreUrl( $params, $template );

?>
<?php if ( $title ?? '' ): ?>
    <h3 class="moredeal-shortcode-title"><?php echo esc_html( $title ?? '' ); ?></h3>
<?php endif; ?>

<div class="feature-template" <?php TemplateHelper::templateGlobalStyle( $isShowMore, $moreUrl, $postType ); ?>
     data-global='<?php echo json_encode( $global ) ?>'>
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
			); ?>
            <div class="moredeal-container moredeal-feature" data-product='<?php echo json_encode( $it ) ?>' style="margin-bottom: 25px">
                <div class="products">
                    <div class="product--horizontal">
						<?php if ( $item['stock_status'] && $item['stock_status'] == '1' ): ?>
                            <span class="ribbon--sale">Sale</span>
						<?php endif; ?>
                        <span class="ribbon--bestseller"
                              style="background-color:  <?php echo TemplateHelper::getCardFeatureSortColor( $sortColor ) ?>">
                            <?php echo TemplateHelper::getCardFeatureSortText( $sortText ) . ( $index + 1 ) ?>
                        </span>
                        <div class="product__thumb">
                            <!-- 图片 -->
							<?php if ( $item['img'] ): ?>
                                <a<?php TemplateHelper::printRel(); ?> target="_blank"
                                                                       href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item) ); ?>">
									<?php TemplateHelper::displayImage( $item, 300, 300 ); ?>
                                </a>
							<?php endif; ?>
                            <div class="product__rating">
								<?php if ( $item['rating'] ): ?>
                                    <div class="moredeal-mb5">
			                            <?php TemplateHelper::printRating2( $item ); ?>
                                    </div>
	                            <?php endif; ?>
	                            <?php if ( $item['commentCount'] ): ?>
                                    <div class="product__reviews"><?php echo $item['commentCount'] ?> Reviews</div>
	                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="product__content">
							<?php if ( $item['title'] ): ?>
                                <a class="product__title" <?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item) ); ?>">
									<?php echo esc_html( $item['title'] ); ?>
                                </a>
							<?php endif; ?>
							<?php if ( $item['description'] ): ?>
                                <div class="product__description">
									<?php TemplateHelper::printDescription( $item, $featureNumber ); ?>
                                </div>
							<?php endif; ?>
                        </div>
                        <div class="product__footer">
                            <div class="product__pricing">
                                <?php if ( $item['stock_status'] == '1' ): ?>
	                                <?php if ( $item['priceOld'] ): ?>
                                        <span class="price--old"><?php echo wp_kses( TemplateHelper::formatPriceCurrency( $item['priceOld'], $item['currencyCode'], '<span class="moredeal-currency">', '</span>' ), array( 'span' => array( 'class' ) ) ) ?></span>
                                        <span class="price--saved"><?php echo wp_kses( TemplateHelper::formatPriceCurrency( ( $item['price'] - $item['priceOld'] ), $item['currencyCode'], '<span class="moredeal-currency">', '</span>' ), array( 'span' => array( 'class' ) ) ) ?></span>
	                                <?php endif; ?>
                                    <span class="price--current"
                                          style="color: <?php echo esc_attr( TemplateHelper::getPriceColor( $template, $priceColor ) ); ?>"><?php echo wp_kses( TemplateHelper::formatPriceCurrency( $item['price'], $item['currencyCode'], '<span class="moredeal-currency">', '</span>' ), array( 'span' => array( 'class' ) ) ) ?></span>
	                                <?php if ( $item['shippingType'] && $item['shippingType'] != '3' ): ?>
                                        <a style="width: 55px;height: 16px"
		                                   <?php TemplateHelper::printRel(); ?>target="_blank"
                                           href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item) ); ?>"></a>
	                                <?php endif; ?>
                                <?php else: ?>
                                    <span class="price"><?php echo esc_html( __( 'Out of stock', 'moredeal' ) ) ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="min-width: 130px; float: right">
                                <a <?php TemplateHelper::printRel(); ?>
                                        target="_blank" href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item) ); ?>"
                                        class="btn btn-danger btn-block"
                                        style="background-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>;
                                                border-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>">
	                                <?php TemplateHelper::buyNowBtnText( true, $item, $btnText, $template ); ?>
                                </a>
                            </div>
                            <span class="product__info">Price incl. tax, excl. shipping</span>
                        </div>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<!-- 这里添加 more 按钮 -->
<?php TemplateHelper::displayMoreButton($isShowMore, $moreUrl)  ?>

