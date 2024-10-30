<?php
/**
 * 模块设置
 */
defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\ModuleController;
use Moredeal\application\components\ModuleManager;
use Moredeal\application\Plugin;

?>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
<!--    <div class="moredeal-maincol">-->
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
            <a href="?page=<?php echo ModuleController::slug ?>"
               class="nav-tab<?php if ( ! empty( $_GET['page'] ) && $_GET['page'] == ModuleController::slug) {
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
                   class="nav-tab<?php if ( ! empty( $_GET['page'] ) && $_GET['page'] == $c->page_slug() ) {
					   echo ' nav-tab-active';
				   } ?>">
                    <span<?php if ( $m->isDeprecated() ): ?> style="color: darkgray;"<?php endif; ?>>
                        <?php echo esc_html( $m->getName() ); ?>
                    </span>
                </a>
			<?php endforeach; ?>
        </h2>

        <div class="moredeal-wrap">
            <div class="moredeal-maincol">
                <h3>
					<?php if (!empty($module) && !$module->isActive() ): ?>
						<?php esc_html_e( 'Add new feed module', 'moredeal' ); ?>
					<?php else: ?>
						<?php echo esc_html( sprintf( __( '%s Settings', 'moredeal' ), $module->getName() ) ); ?>
					<?php endif; ?>
					<?php if ( $docs_uri = $module->getDocsUri() ) {
						echo sprintf( '<a target="_blank" class="page-title-action" href="%s">' . esc_html( __( 'Documentation', 'moredeal' ) ) . '</a>', esc_url_raw( $docs_uri ) );
					} ?>
                </h3>

				<?php if ( ! empty( $module ) && $requirements = $module->requirements() ): ?>
                    <div class="moredeal-warning">
                        <strong>
							<?php esc_html_e( 'WARNING:', 'moredeal' ); ?>
							<?php esc_html_e( 'This module cannot be activated!', 'moredeal' ) ?>
							<?php esc_html_e( 'Please fix the following error(s):', 'moredeal' ) ?>
                            <ul>
                                <li><?php echo wp_kses_post( join( '</li><li>', $requirements ) ); ?></li>
                            </ul>
                        </strong>
                    </div>
				<?php endif; ?>

				<?php settings_errors(); ?>
                <?php if ( ! empty( $config ) ): ?>
                    <form action="options.php" method="POST">
                        <?php settings_fields( $config->page_slug() ); ?>
                        <table class="form-table">
                            <?php do_settings_sections( $config->page_slug() ); ?>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php if ( Plugin::isFree() || Plugin::isInactiveEnvato() ): ?>
<!--    </div>-->
	<?php include( '_promo_box.php' ); ?>
<?php endif; ?>