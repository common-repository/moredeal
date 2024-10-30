<?php
/**
 * 商品列表页
 */
defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\ProductController;
use Moredeal\application\Plugin;

$message = '';
?>

<div id="seastar_moredeal_waiting_products" style="display:none; text-align: center;">
    <h2><?php esc_html_e( 'Updating... Please wait...', 'moredeal' ); ?></h2>
    <p>
        <img src="<?php echo esc_url_raw( \Moredeal\PLUGIN_RES ); ?>/img/moredeal_waiting.gif" ;/>
    </p>
</div>
<script type="text/javascript">
    let $j = jQuery.noConflict();
    $j(document).ready(function () {
        $j('#btn_scan_products').click(function () {
            $j.blockUI({message: $j('#seastar_moredeal_waiting_products')});
        });
    });
</script>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
<div class="moredeal-maincol">
	<?php endif; ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">
			<?php esc_html_e( 'Moredeal Products', 'moredeal' ); ?>
        </h1>
        <a id="btn_scan_products"
           href="<?php echo esc_url_raw( get_admin_url( get_current_blog_id(), 'admin.php?page=' . ProductController::slug . '&action=update' ) ); ?>"
           class="page-title-action"><?php esc_html_e( 'Update Products', 'moredeal' ); ?></a>
        &nbsp;<small><?php echo esc_html( sprintf( __( 'Last Update: %s', 'moredeal' ), $last_scaned_str ?? '' ) ); ?></small>

		<?php echo wp_kses_post( $message ); ?>

		<?php if ( ! empty( $table ) ): ?>
            <form id="moredeal-products-table" method="GET">
                <input type="hidden" name="page" value="moredeal-product"/>
				<?php if ( isset( $_REQUEST['stock_status'] ) ): ?>
                    <input type="hidden" name="stock_status"
                           value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['stock_status'] ) ) ); ?>"/>
				<?php endif; ?>
				<?php $table->views(); ?>
				<?php $table->search_box( __( 'Search products', 'moredeal' ), 'key' ); ?>
				<?php $table->display(); ?>
            </form>
		<?php endif; ?>
    </div>

	<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
</div>
<?php include( '_promo_box.php' ); ?>
<?php endif; ?>        