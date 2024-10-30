<?php
/*
 * Name: Top listing
 * Modules:
 * Module Types: PRODUCT
 *
 */

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;

$data       = TemplateHelper::getData( $data ?? array(), $limit ?? null );
$ratings    = TemplateHelper::generateStaticRatings( count( $data ?? array() ) );
$post_id    = $post_id ?? get_the_ID();
$postType   = $postType ?? get_post_type( $post_id );
$postTitle  = $post_title ?? get_the_title( $post_id );
$template   = $template ?? 'block_top_listing';
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

<div class="moredeal-container moredeal-top-listing">
	<?php if ( $title ?? '' ): ?>
        <h3><?php echo esc_html( $title ?? '' ); ?></h3>
	<?php endif; ?>
    <div class="moredeal-listcontainer">
		<?php if ( ! empty( $data ) ): ?>
            <div class="top-template" <?php TemplateHelper::templateGlobalStyle( $isShowMore, $moreUrl, $postType ); ?>
                 data-global='<?php echo json_encode( $global ) ?>'>
				<?php foreach ( $data as $i => $item ): $item = (array) $item ?>
					<?php $it = array(
						'post_id'         => $item['post_id'],
						'product_code'    => $item['code'],
						'category_id'     => $item['category_id'],
						's_id'            => $item['s_id'],
						'trace_id'        => $item['trace_id'],
						'view_id'         => $item['view_idx'],
						'search_location' => $item['search_location'],
					) ?>
                    <div class="row-products row" data-product='<?php echo json_encode( $it ) ?>'>
                        <div class="col-md-2 col-sm-2 col-xs-3 moredeal-image-cell">
                            <!-- 序号 -->
                            <div class="moredeal-position-container2">
                                <span class="moredeal-position-text2"><?php echo (int) $i + 1; ?></span>
                            </div>
                            <!-- 图片 -->
							<?php if ( $item['img'] ): ?>
                                <a<?php TemplateHelper::printRel(); ?> target="_blank"
                                                                       href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
									<?php TemplateHelper::displayImage( $item, 120, 100 ); ?>
                                </a>
							<?php endif; ?>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 moredeal-desc-cell">
							<?php if ( strstr( $item['description'], 'class="label' ) ): ?>
								<?php echo wp_kses_post( $item['description'] ); ?>
							<?php else: ?>

								<?php /*if ( $i == 0 && TemplateHelper::getChance( $i ) ): */ ?><!--
                                <span class="label label-success">&check; <?php /*esc_html_e( 'Best choice', 'moredeal' ); */ ?></span>
							<?php /*elseif ( $i == 1 && TemplateHelper::getChance( $i ) ): */ ?>
                                <span class="label label-success"><?php /*esc_html_e( 'Recommended', 'moredeal' ); */ ?></span>
							<?php /*elseif ( $i == 2 && TemplateHelper::getChance( $i ) ): */ ?>
                                <span class="label label-success"><?php /*esc_html_e( 'High quality', 'moredeal' ); */ ?></span>
							--><?php /*endif; */ ?>
							<?php endif; ?>
                            <!-- 标题 -->
                            <div class="moredeal-no-top-margin moredeal-list-logo-title ">
                                <a class="moredeal-product-title-tip"
                                   target="_blank" <?php TemplateHelper::printRel(); ?>
                                   href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>"
                                   data-title="<?php echo TemplateHelper::printTitle( $item, 100000 ) ?>">
									<?php echo esc_html( TemplateHelper::truncate( $item['title'], 100 ) ); ?>
                                </a>
                            </div>
                        </div>
                        <!-- 圆环 -->
                        <div class="col-md-2 col-sm-2 col-xs-3">
							<?php TemplateHelper::printProgressRing( $ratings[ $i ] ); ?>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 moredeal-btn-cell hidden-xs">
                            <div class="moredeal-btn-row">
                                <a<?php TemplateHelper::printRel(); ?> target="_blank"
                                                                       href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>"
                                                                       class="btn btn-danger btn-block"
                                                                       style="background-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>;
                                                                               border-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ) ); ?>">
                                    <span><?php TemplateHelper::buyNowBtnText( true, $item, $btnText, $template ); ?></span>
                                </a>
                            </div>
                            <!-- 商户名称 -->
	                        <?php if ( $merchant = TemplateHelper::merchantName( $item ) ): ?>
                                <div class="text-center">
                                    <small class="text-muted title-case">
				                        <?php echo esc_html( $merchant ); ?>
				                        <?php TemplateHelper::printShopInfo( $item ); ?>
                                    </small>
                                </div>
	                        <?php endif; ?>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>
    </div>
</div>
<!-- 这里添加 more 按钮 -->
<?php TemplateHelper::displayMoreButton( $isShowMore, $moreUrl ) ?>
