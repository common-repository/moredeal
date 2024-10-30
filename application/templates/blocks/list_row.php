<?php

defined('\ABSPATH') || exit;

use Moredeal\application\helpers\TemplateHelper;

?>
<?php if ( ! empty( $item ) ): ?>
    <!-- 隐藏标题 -->
    <div class="moredeal-list-logo-title moredeal-mt5 moredeal-mb15 visible-xs text-center">
        <a <?php TemplateHelper::printRel(); ?> target="_blank"
                                                href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
			<?php echo esc_html( TemplateHelper::truncate( $item['title'], 100 ) ); ?>
        </a>
    </div>
    <div class="row-products">
        <div class="col-md-2 col-sm-2 col-xs-12 moredeal-image-cell">
            <!-- 图片 -->
			<?php if ( $item['img'] ): ?>
                <a <?php TemplateHelper::printRel(); ?> target="_blank"
                                                        href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
					<?php TemplateHelper::displayImage( $item, 130, 100 ); ?>
                </a>
			<?php endif; ?>
        </div>
        <!-- 标题 -->
        <div class="col-md-5 col-sm-5 col-xs-12 moredeal-desc-cell hidden-xs">
            <div class="moredeal-no-top-margin moredeal-list-logo-title">
                <a class="moredeal-product-title-tip"
                   target="_blank" <?php TemplateHelper::printRel(); ?>
                   data-title="<?php echo TemplateHelper::printTitle( $item, 100000 ) ?>"
                   href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
					<?php echo esc_html( TemplateHelper::truncate( $item['title'], 100 ) ); ?>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-12 moredeal-price-cell text-center">
            <div class="moredeal-price-row">
                <!-- 现价 -->
				<?php if ( $item['price'] ): ?>
                    <div class=" moredeal-price-<?php echo esc_attr( TemplateHelper::getStockStatusClass( $item ) ); ?>"
                         style="color: <?php echo esc_attr( TemplateHelper::getPriceColor( $template ?? 'block_offers_list', $priceColor ?? '' ) ); ?>; font-weight: bold; font-size: 1.1em;">
						<?php echo esc_html( TemplateHelper::formatPriceCurrency( $item['price'], $item['currencyCode'] ) ); ?>
                    </div>
				<?php endif; ?>
                <!-- 老价格 -->
				<?php if ( $item['priceOld'] ): ?>
                    <div class="text-muted">
                        <s><?php echo esc_html( TemplateHelper::formatPriceCurrency( $item['priceOld'], $item['currencyCode'] ) ); ?></s>
                    </div>
				<?php endif; ?>
                <!-- 库存状态 -->
				<?php if ( $stock_status = TemplateHelper::getStockStatusStr( $item ) ): ?>
                    <div title="<?php echo esc_attr( sprintf( TemplateHelper::__( 'Last updated on %s' ), TemplateHelper::getLastUpdateFormatted( $item['lastUpdate'] ) )); ?>"
                         class="moredeal-lineheight15 stock-status status-<?php echo esc_attr( TemplateHelper::getStockStatusClass( $item ) ); ?>">
						<?php echo esc_html( $stock_status ); ?>
                    </div>
				<?php endif; ?>
                <!-- 若模块 id 为 Amazon -->
				<?php if ( $item['module_id'] == 'Amazon'): ?>
                    <div class="moredeal-font60 moredeal-lineheight15">
						<?php echo esc_html( sprintf( TemplateHelper::__( 'as of %s' ), TemplateHelper::getLastUpdateFormatted( $item['lastUpdate'] ) ) ); ?>
						<?php TemplateHelper::printAmazonDisclaimer(); ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-12 moredeal-btn-cell">
            <!-- 按钮 -->
            <div class="moredeal-btn-row">
                <a <?php TemplateHelper::printRel(); ?>
                        target="_blank" href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item)); ?>"
                        class="btn btn-danger btn-block"
                        style="background-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ?? '' ) ); ?>;
                                border-color: <?php echo esc_attr( TemplateHelper::getButtonColor( $template, $btnColor ?? '' ) ); ?>">
                    <span><?php TemplateHelper::buyNowBtnText( true, $item, $btnText ?? '', $template ); ?></span>
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
<?php endif; ?>


