<?php

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\AdminHelper;
use Moredeal\application\Plugin;

?>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
    <div class="moredeal-maincol">
<?php endif; ?>

    <div class="wrap">
        <h2>
			<?php esc_html_e( 'Moredeal Settings', 'moredeal' ); ?>
			<?php if ( Plugin::isPro() ): ?>
                <span class="moredeal-label moredeal-label-pro">pro</span>
			<?php else: ?>
                <a target="_blank" class="page-title-action" href="<?php echo esc_url_raw( Plugin::pluginGoProUrl() );  ?>"><?php esc_html_e('Go Pro', 'moredeal'); ?></a>
			<?php endif; ?>
        </h2>

		<?php settings_errors(); ?>
	    <?php if ( ! empty( $page_slug ) ): ?>
            <form action="options.php" method="POST">
			    <?php settings_fields( $page_slug ); ?>
			    <?php AdminHelper::doTabsSections( $page_slug ); ?>
			    <?php submit_button(); ?>
            </form>
	    <?php endif; ?>
    </div>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
    </div>
	<?php include( '_promo_box.php' ); ?>
<?php endif; ?>