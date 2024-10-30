<?php
/**
 * 模块设置首页
 */
defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\ModuleController;
use Moredeal\application\components\ModuleManager;
use Moredeal\application\helpers\AdminHelper;
use Moredeal\application\Plugin;

?>

<?php

function _seastar_moredeal_print_module_item( array $modules ) {
	foreach ( $modules as $module ) {
		echo '<a href="?page=' . esc_attr( $module->getConfigInstance()->page_slug() ) . '" class = "list-group-item">';
		echo esc_html( $module->getName() );
		if ( $module->isActive() && ! $module->isDeprecated() ) {
			echo '<span class="label label-success" style="float: right">' . esc_html( __( 'Active', 'moredeal' ) ) . '</span>';
		}
		if ( $module->isDeprecated() ) {
			echo '<span class="label label-warning" style="float: right">' . esc_html( __( 'Deprecated', 'moredeal' ) ) . '</span>';
		}
		if ( $module->isNew() ) {
			echo '<span class="label label-info" style="float: right">' . esc_html( __( 'New', 'moredeal' ) ) . '</span>';
		}
		echo '</a>';
	}
}

?>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
<div class="moredeal-maincol">
	<?php endif; ?>

    <div class="wrap">
        <h2>
			<?php esc_html_e( 'Moredeal Module Settings', 'moredeal' ); ?>
            <?php if ( Plugin::isPro() ): ?>
                <span class="moredeal-label moredeal-label-pro">pro</span>
            <?php else: ?>
                <a target="_blank" class="page-title-action" href="<?php echo esc_url_raw( Plugin::pluginGoProUrl() );  ?>"><?php esc_html_e('Go Pro', 'moredeal'); ?></a>
            <?php endif; ?>
        </h2>

        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo esc_attr( ModuleController::slug ); ?>"
               class="nav-tab<?php if ( ! empty( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) == ModuleController::slug ) {
				   echo ' nav-tab-active';
			   } ?>">
                <span class="dashicons dashicons-menu-alt3"></span>
            </a>
			<?php foreach ( ModuleManager::getInstance()->getConfigurableModules( true ) as $m ): ?>
				<?php if ( $m->isDeprecated() && ! $m->isActive() ) {
					continue;
				} ?>
				<?php $c = $m->getConfigInstance(); ?>
                <a href="?page=<?php echo esc_attr( $c->page_slug() ); ?>"
                   class="nav-tab<?php if ( ! empty( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) == $c->page_slug() ) {
					   echo ' nav-tab-active';
				   } ?>">
                    <span<?php if ( $m->isDeprecated() ): ?> style="color: darkgray;"<?php endif; ?>>
                        <?php echo esc_html( $m->getName() ); ?>
                    </span>
                </a>
			<?php endforeach; ?>
        </h2>

        <br/>
        <div class="moredeal-container">
            <div class="row">
                <div class="col-md-4 col-xs-12">

                    <div class="panel panel-default">
                        <div class="panel-heading"><h3
                                    class="panel-title"><?php esc_html_e( 'Product modules', 'moredeal' ); ?></h3>
                        </div>
                        <div class="list-group">
							<?php _seastar_moredeal_print_module_item( AdminHelper::getProductModules() ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
</div>
<?php include( '_promo_box.php' ); ?>
<?php endif; ?>  