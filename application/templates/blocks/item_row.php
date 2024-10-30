<?php

defined('\ABSPATH') || exit;

use Moredeal\application\helpers\TemplateHelper;

?>

<div class="row">
    <?php if ( ! empty( $item ) ): ?>
        <div class="col-md-5 text-center moredeal-image-container moredeal-mb20">
            <!-- 图片 -->
		    <?php if ( $item['img'] ): ?>
                <a<?php TemplateHelper::printRel(); ?>  target="_blank" href="<?php echo esc_url_raw( TemplateHelper::parseUrl($item) ); ?>">
				    <?php TemplateHelper::displayImageWidth( $item, 250, 250, 220 ); ?>
                </a>
		    <?php endif; ?>
        </div>
        <div class="col-md-7">
            <!-- 标题 -->
		    <?php if ( $item['title'] ): ?>
                <h3 class="moredeal-item-title">
                    <a target="_blank" <?php TemplateHelper::printRel(); ?>
                       style="color: #2b2b2b"
                       href="<?php echo esc_url_raw( TemplateHelper::parseUrl( $item ) ); ?>">
					    <?php echo esc_html( $item['title'] ); ?>
                    </a>
                </h3>
		    <?php endif; ?>

            <!-- 评分 -->
		    <?php if ( $item['rating'] ): ?>
                <div class="moredeal-mb5">
				    <?php TemplateHelper::printRating( $item ); ?>
                </div>
		    <?php endif; ?>

            <div class="moredeal-price-row">
                <!-- 现价 -->
			    <?php if ( $item['price'] ): ?>
                    <span class="moredeal-price moredeal-price-color"
                          style="color: <?php echo esc_attr(TemplateHelper::getPriceColor($template ?? 'block_item', $priceColor ?? '')); ?>;
                                  font-size: 32px;line-height: 30px;white-space: nowrap; font-weight: bold;margin-bottom: 15px;margin-top: 15px;display: inline-block;">
                    <!-- 老价格 -->
                    <?php if ( $item['priceOld'] ): ?>
                        <small class="text-muted"><s><?php echo wp_kses( TemplateHelper::formatPriceCurrency( $item['priceOld'], $item['currencyCode'], '<small>', '</small>' ), array( 'small' => array() ) ); ?></s></small>
                        <br>
                    <?php endif; ?>
					    <?php echo wp_kses( TemplateHelper::formatPriceCurrency( $item['price'], $item['currencyCode'], '<span class="moredeal-currency">', '</span>' ), array( 'span' => array( 'class' ) ) ); ?></span>
			    <?php endif; ?>

                <!-- 库存 -->
			    <?php if ( $stock_status = TemplateHelper::getStockStatusStr( $item ) ): ?>
                    <mark title="<?php echo esc_attr( sprintf( TemplateHelper::__( 'Last updated on %s' ), TemplateHelper::getLastUpdateFormatted( $item['lastUpdate'] ) ) ); ?>"
                          class="stock-status status-<?php echo esc_attr( TemplateHelper::getStockStatusClass( $item ) ); ?>">
                        &nbsp;<?php echo esc_html( $stock_status ); ?>
                    </mark>
			    <?php endif; ?>

			    <?php if ( $cashback_str = TemplateHelper::getCashbackStr( $item ) ): ?>
                    <div class="moredeal-cashback"><?php echo esc_html( sprintf( TemplateHelper::__( 'Plus %s Cash Back' ), $cashback_str ) ); ?></div>
			    <?php endif; ?>
            </div>

		    <?php $this->renderBlock( 'item_after_price_row', array( 'item' => (array)$item ) ); ?>

            <div class="moredeal-btn-row moredeal-mb5">
                <div>
                    <a<?php TemplateHelper::printRel(); ?> target="_blank"
                                                           href="<?php echo esc_url_raw(TemplateHelper::parseUrl($item)); ?>"
                                                           class="btn btn-danger moredeal-btn-big"
                                                           style="background-color: <?php echo esc_attr(TemplateHelper::getButtonColor($template, $btnColor ?? '')); ?>;
                                                                   border-color: <?php echo esc_attr(TemplateHelper::getButtonColor($template, $btnColor ?? '')); ?>">
                        <?php TemplateHelper::buyNowBtnText(true, $item, $btnText ?? '', $template); ?>
                    </a>
                </div>
                <span class="title-case text-muted">
                <?php TemplateHelper::merchantName($item, true); ?>
                <?php TemplateHelper::printShopInfo($item, array('data-placement' => 'bottom')); ?>
            </span>
            </div>
            <div class="moredeal-last-update-row moredeal-mb15">
            <span class="text-muted">
                <small>
                    <?php echo esc_html(sprintf(TemplateHelper::__('as of %s'), TemplateHelper::getLastUpdateFormatted($item['lastUpdate']))); ?><?php
                    if ($item['module_id'] == 'Amazon') {
                        TemplateHelper::printAmazonDisclaimer();
                    }
                    ?>
                </small>
            </span>
            </div>
        </div>
    <?php endif; ?>
</div>

