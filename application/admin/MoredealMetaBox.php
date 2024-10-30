<?php

namespace Moredeal\application\components;

use Exception;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;

/**
 * MoredealMetaBox class file
 *
 * @author Aclumsy
 */
class MoredealMetaBox {

	/**
	 * seastarMetaData
	 * @var array $metaData
	 */
	private array $seastarMetaData = array();

	/**
	 * 单例实例
	 * @var null|MoredealMetaBox $instance 单例实例
	 */
	private static ?MoredealMetaBox $instance = null;

	/**
	 * 获取单例实例
	 * @return MoredealMetaBox|null
	 */
	public static function getInstance(): ?MoredealMetaBox {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, "initRenderMoredealBox" ) );
		add_action( 'save_post', array( $this, 'saveMeta' ) );
	}

	/**
	 * 获取 Seastar Meta Data
	 *
	 * @return array
	 */
	public function getSeastarMetaData(): array {
		return $this->seastarMetaData;
	}

	/**
	 * 添加 Seastar Meta Data
	 *
	 * @param $key string Meta Key
	 * @param $value object|array|int  Meta Value
	 *
	 * @return void
	 */
	public function addSeastarMetaData( string $key, $value ) {
		$this->seastarMetaData[ $key ] = $value;
	}

	/**
	 * 初始化渲染 Moredeal Box
	 *
	 * @param $post_type
	 *
	 * @return void
	 */
	function initRenderMoredealBox( $post_type ) {

//		if ( ! in_array( $post_type, GeneralConfig::getInstance()->option( 'post_types' ) ) ) {
//			return;
//		}

		$title = 'Moredeal';

		if (Plugin::isPro()) {
			$title .= ' Pro';
		}
		$title .= '&nbsp;<span><a target="_blank" style="font-size: 150%; color: #2396fa" href="' . Plugin::pluginDocsUrl() . '">' . __( 'user guide', 'moredeal' ) . '</a>';

		$feedback = \Moredeal\PLUGIN_RES . '/css/images/feedback.svg';
		$title .= '&nbsp;&nbsp;<span><a target="_blank" href="'.Plugin::pluginFeedBackUrl().'" style="padding-right: 12px;"><img style="position: absolute; bottom: auto;" width="35px" src="'. $feedback.'"  alt=""/></a></span></span>';

		add_meta_box( 'moredeal_meta_box', $title, array(
			$this,
			"renderSeastarbox"
		), $post_type, 'normal', 'high' );
		// 获取初始化数据
		$this->initMetaData();
	}

	/**
	 * 渲染 Moredeal Box
	 * @return void
	 */
	function renderSeastarbox() {
		// 加载通用静态文件
		$this->admin_load_scripts();
		echo "<div id='app'></div>";
	}

	/**
	 * 保存文章时候把商品保存到 post meta 中
	 *
	 * @param $post_id
	 *
	 * @return void
	 * @throws Exception
	 */
	function saveMeta( $post_id ) {

		/**
		 * 流程
		 * 1，接受提交中到必要信息，包括 选择了哪些商品，通过 POST 直接提交
		 * 2，接收参数处理，保存到 meta 中
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// 判断当前用户是否有操作权限
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'page' ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		// 获取商品数据
		if ( ! isset( $_POST['seastar_data'] ) ) {
			return;
		}
		$seastar_data = wp_unslash( sanitize_text_field( $_POST['seastar_data'] ) );
		MoredealManager::getInstance()->saveMeta( $post_id, $seastar_data );
	}

	/**
	 * 初始化 MetaBox 数据
	 * @return void
	 * @throws Exception
	 */
	public function initMetaData() {
		global $post;
		$post_id = $post->ID;
		// 向元数据中添加数据
		$this->addSeastarMetaData( 'postId', $post_id );
		// metaBox 数据
		$this->addSeastarMetaData( "metaBox", MoredealManager::getInstance()->obtainMetaBox( $post_id ) );
		// 模版数据
		$this->addSeastarMetaData( "templateList", MoredealManager::getTemplate() );
		// 模块模版数据
		$this->addSeastarMetaData( "moduleTemplateList", MoredealManager::getModuleTemplate() );
	}

	/**
	 * 加载国际化文件信息
	 * @return array
	 */
	function getLocaleMessage(): array {
		return array(
			'lang' => get_locale(),
			'message' => array(
				'title.search'                   => __( 'title.search', 'moredeal' ),
				'title.reset'                    => __( 'title.reset', 'moredeal' ),
				'title.tips'                     => __( 'title.tips', 'moredeal' ),
				'title.exclude'                  => __( 'title.exclude', 'moredeal' ),
				'title.category'                 => __( 'title.category', 'moredeal' ),
				'goods.title'                    => __( 'goods.title', 'moredeal' ),
				'goods.name'                     => __( 'goods.name', 'moredeal' ),
				'goods.source'                   => __( 'goods.source', 'moredeal' ),
				'goods.price'                    => __( 'goods.price', 'moredeal' ),
				'goods.unit'                     => __( 'goods.unit', 'moredeal' ),
				'goods.mark'                     => __( 'goods.mark', 'moredeal' ),
				'table.product'                  => __( 'table.product', 'moredeal' ),
				'table.globalScore'              => __( 'table.globalScore', 'moredeal' ),
				'table.price'                    => __( 'table.price', 'moredeal' ),
				'table.salesCount'               => __( 'table.salesCount', 'moredeal' ),
				'table.sales'                    => __( 'table.sales', 'moredeal' ),
				'table.currentBsr'               => __( 'table.currentBsr', 'moredeal' ),
				'table.commentCount'             => __( 'table.commentCount', 'moredeal' ),
				'table.star'                     => __( 'table.star', 'moredeal' ),
				'table.qa'                       => __( 'table.qa', 'moredeal' ),
				'table.volume'                   => __( 'table.volume', 'moredeal' ),
				'table.firstDate'                => __( 'table.firstDate', 'moredeal' ),
				'table.changeTime'               => __( 'table.changeTime', 'moredeal' ),
				'tip.product'                    => __( 'tip.product', 'moredeal' ),
				'tip.globalScore'                => __( 'tip.globalScore', 'moredeal' ),
				'tip.price'                      => __( 'tip.price', 'moredeal' ),
				'tip.salesCount'                 => __( 'tip.salesCount', 'moredeal' ),
				'tip.sales'                      => __( 'tip.sales', 'moredeal' ),
				'tip.currentBsr'                 => __( 'tip.currentBsr', 'moredeal' ),
				'tip.commentCount'               => __( 'tip.commentCount', 'moredeal' ),
				'tip.star'                       => __( 'tip.star', 'moredeal' ),
				'tip.qa'                         => __( 'tip.qa', 'moredeal' ),
				'tip.volume'                     => __( 'tip.volume', 'moredeal' ),
				'tip.firstDate'                  => __( 'tip.firstDate', 'moredeal' ),
				'tip.changeTime'                 => __( 'tip.changeTime', 'moredeal' ),
				'selection.strategy'             => __( 'selection.strategy', 'moredeal' ),
				'selection.order'                => __( 'selection.order', 'moredeal' ),
				'other.hiddenCondition'          => __( 'other.hiddenCondition', 'moredeal' ),
				'other.showCondition'            => __( 'other.showCondition', 'moredeal' ),
				'other.max'                      => __( 'other.max', 'moredeal' ),
				'other.min'                      => __( 'other.min', 'moredeal' ),
				'other.lineTip'                  => __( 'other.lineTip', 'moredeal' ),
				'other.variant'                  => __( 'other.variant', 'moredeal' ),
				'other.brand'                    => __( 'other.brand', 'moredeal' ),
				'other.parentAsin'               => __( 'other.parentAsin', 'moredeal' ),
				'other.categoryRank'             => __( 'other.categoryRank', 'moredeal' ),
				'other.subcategoryRank'          => __( 'other.subcategoryRank', 'moredeal' ),
				'other.copySuccess'              => __( 'other.copySuccess', 'moredeal' ),
				'other.pasteOperation'           => __( 'other.pasteOperation', 'moredeal' ),
				'other.insertSuccessful'         => __( 'other.insertSuccessful', 'moredeal' ),
				'other.theProductAlreadyExists'  => __( 'other.theProductAlreadyExists', 'moredeal' ),
				'other.insertFailed'             => __( 'other.insertFailed', 'moredeal' ),
				'other.systemError'              => __( 'other.systemError', 'moredeal' ),
				'selection.strategy.tip'         => __( 'selection.strategy.tip', 'moredeal' ),
				'unlimited'                      => __( 'unlimited', 'moredeal' ),
				'selection.hotSearchKeyword'     => __( 'selection.hotSearchKeyword', 'moredeal' ),
				'selection.hotSearchKeyword.tip' => __( 'selection.hotSearchKeyword.tip', 'moredeal' ),
			)
		);

	}

	/**
	 * 加载通用静态文件
	 *
	 * @return void
	 */
	function admin_load_scripts() {
		wp_enqueue_script( 'moredeal-sortable', \Moredeal\PLUGIN_RES . '/js/search/sortable.min.js' );
		wp_enqueue_script( 'moredeal-vuedraggable', \Moredeal\PLUGIN_RES . '/js/search/vuedraggable.min.js' );
		wp_enqueue_script( 'moredeal-vue', \Moredeal\PLUGIN_RES . '/js/search/vue.min.js' );
		wp_enqueue_script( 'moredeal-treeselect', \Moredeal\PLUGIN_RES . '/js/search/treeselect.js' );
		wp_enqueue_script( 'moredeal-sdk', \Moredeal\PLUGIN_RES . '/js/sensorsdata.min.js' );
		wp_enqueue_script( 'moredeal-init', \Moredeal\PLUGIN_RES . '/js/point/sensors_init.js' );
		wp_enqueue_script( 'moredeal-axios', \Moredeal\PLUGIN_RES . '/js/search/axios.min.js' );
		wp_enqueue_script( 'moredeal-element-ui', \Moredeal\PLUGIN_RES . '/js/search/element_ui.min.js' );

		wp_enqueue_style( 'moredeal-element_ui', \Moredeal\PLUGIN_RES . '/js/search/element_ui.css' );
		wp_enqueue_style( 'moredeal-treeselects', \Moredeal\PLUGIN_RES . '/js/search/treeselect.css' );
		wp_enqueue_style( 'moredeal-search', \Moredeal\PLUGIN_RES . '/css/search.css' );

		wp_register_script( 'moredeal-metabox_rest', \Moredeal\PLUGIN_RES . '/js/request.js', array(), Plugin::version() );
		wp_localize_script( 'moredeal-metabox_rest', 'REST_URL', array( 'restUrl' => untrailingslashit( esc_url_raw( rest_url() ) ) ) );
		wp_enqueue_script( 'moredeal-metabox_rest' );

		wp_enqueue_script( 'moredeal-goods', \Moredeal\PLUGIN_RES . '/js/goods.js', array(), Plugin::version() );

		wp_register_script( 'moredeal-metabox_app', \Moredeal\PLUGIN_RES . '/js/search.js', array(), Plugin::version() );
		wp_localize_script( 'moredeal-metabox_app', 'seastarMetaData', MoredealMetabox::getInstance()->getSeastarMetadata() );
		wp_localize_script( 'moredeal-metabox_app', 'point', array(
			'isPro'            => Plugin::isPro(),
			'moredeal_version' => Plugin::version(),
			'wp_version'       => get_bloginfo( 'version' ),
			'wp_addr'          => SearchProductClient::getCurrDomain(),
			'auth_code'        => Plugin::getAuthCode(),
			'post_title'       => get_the_title(),
		) );
		wp_localize_script( 'moredeal-metabox_app', 'localeMessage', MoredealMetabox::getInstance()->getLocaleMessage() );
		wp_enqueue_script( 'moredeal-metabox_app' );
	}


}