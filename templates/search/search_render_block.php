<?php
/*
 * Name: Product Search
 * Modules:
 * Module Types: PRODUCT
 */
defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\SearchProductClient;
use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\Plugin;

$post_id              = $post_id ?? get_the_ID();
$postType             = $postType ?? get_post_type( $post_id );
$postTitle            = $post_title ?? get_the_title( $post_id );
$template             = $template ?? 'render_block';
$params               = $params ?? array();
$params['post_id']    = empty( $post_id ) ? get_the_ID() : $post_id;
$params['post_title'] = empty( $post_title ) ? get_the_title( $post_id ) : $post_title;
$searchTemplate       = array_key_exists( 'search_template', $params ) ? $params['search_template'] : 'default';
if ( ! in_array( $searchTemplate, AttributeHelper::getCanRenderSearchTemplate() ) ) {
	$searchTemplate = 'default';
}
error_log( 'searchTemplate: ' . $searchTemplate );
$global      = array(
	'post_id'          => $post_id,
	'post_title'       => $postTitle,
	'template'         => $template,
	'auth_code'        => empty( $auth_code ) ? '' : $auth_code,
	'isPro'            => empty( $isPro ) ? false : $isPro,
	'moredeal_version' => empty( $moredeal_version ) ? '' : $moredeal_version,
	'wp_version'       => empty( $wp_version ) ? '' : $wp_version,
	'wp_addr'          => empty( $wp_addr ) ? '' : $wp_addr,
	'searchTemplate'   => $searchTemplate,
);
$searchKey   = array_key_exists( 'search_key', $params ) && $params['search_key'] ? $params['search_key'] : '';
$categoryIds = array();
$hotKeywords = '';
if ( ! empty( $searchKey ) ) {
	$keys = explode( '_', $searchKey );
	if ( count( $keys ) >= 1 ) {
		$hotKeywords    = $keys[0];
		$categoryIdList = array_splice( $keys, 1 );
		if ( count( $categoryIdList ) > 0 ) {
			foreach ( $categoryIdList as $categoryId ) {
				$categoryId = intval( $categoryId );
				if ( $categoryId > 0 ) {
					$categoryIds[] = $categoryId;
				}
			}
		}
	}
}
$searchLimit = array_key_exists( 'search_limit', $params ) && $params['search_limit'] ? $params['search_limit'] : 10;
if ( $searchLimit <= 0 ) {
	$searchLimit = 10;
}
if ( $searchLimit > 20 ) {
	$searchLimit = 20;
}

wp_register_script( 'moredeal-search-render-block-script', \Moredeal\PLUGIN_RES . '/js/temVue/search_render_block.js', array(), Plugin::version() );
wp_localize_script( 'moredeal-search-render-block-script', 'point', array(
	'isPro'            => Plugin::isPro(),
	'moredeal_version' => Plugin::version(),
	'wp_version'       => get_bloginfo( 'version' ),
	'wp_addr'          => SearchProductClient::getCurrDomain(),
	'auth_code'        => Plugin::getAuthCode(),
	'post_title'       => get_the_title(),
) );
wp_localize_script( 'moredeal-search-render-block-script', 'localeSearchData', array(
	'searchLimit'    => intval( $searchLimit ),
	'searchTemplate' => $searchTemplate,
	'conditionType'  => array_key_exists( 'search_condition_type', $params ) ? $params['search_condition_type'] : array(
		'hotKeywords',
		'selectionStrategy',
		'selectionConditions',
	),
	'hotKeywords'    => $hotKeywords ?? '',
	'categoryIds'    => count( $categoryIds ) > 0 ? array( $categoryIds[0] ) : array(),
	'attributes'     => $params,
	'renderAble'     => AttributeHelper::getCanRenderSearchTemplate(),

) );
wp_localize_script( 'moredeal-search-render-block-script', 'localeMessage', empty( $locale_message ) ? array() : $locale_message );
wp_localize_script( 'moredeal-search-render-block-script', 'global', $global );
wp_enqueue_script( 'moredeal-search-render-block-script' );
?>
<?php if ( $title ?? '' ): ?>
    <h3 class="moredeal-shortcode-title"><?php echo esc_html( $title ?? '' ); ?></h3>
<?php endif; ?>
<div id="search"></div>
