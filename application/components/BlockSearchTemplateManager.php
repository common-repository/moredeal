<?php

namespace Moredeal\application\components;

defined('\ABSPATH') || exit;

/**
 * block模版渲染管理
 */
class BlockSearchTemplateManager extends TemplateManager {

	/**
	 * @var string 模版路径
	 */
	const TEMPLATE_DIR = 'templates/search';

	/**
	 * @var string 自定义模版路径
	 */
	const CUSTOM_TEMPLATE_DIR = 'moredeal-templates/search';

	/**
	 * @var string 模版前缀
	 */
	const TEMPLATE_PREFIX = 'search_';

	/**
	 * @var BlockSearchTemplateManager|null 实例对象
	 */
	private static ?BlockSearchTemplateManager $instance = null;

	/**
	 * 获取单例
	 * @return BlockSearchTemplateManager|null
	 */
	public static function getInstance(): ?BlockSearchTemplateManager {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
	}

	/**
	 * 获取模版路径前缀
	 * @return string
	 */
	public function getTemplatePrefix(): string {
		return self::TEMPLATE_PREFIX;
	}

	/**
	 * 获取模版路径
	 * @return string
	 */
	public function getTemplateDir(): string {
		return \Moredeal\PLUGIN_PATH . self::TEMPLATE_DIR;
	}

	/**
	 * 获取自定义模版路径
	 * @return array
	 */
	public function getCustomTemplateDirs(): array {
		return array(
			'child-theme' => get_stylesheet_directory() . '/' . self::CUSTOM_TEMPLATE_DIR, //child theme
			'theme'       => get_template_directory() . '/' . self::CUSTOM_TEMPLATE_DIR, // theme
			'custom'      => WP_CONTENT_DIR . '/' . self::CUSTOM_TEMPLATE_DIR,
		);
	}

	/**
	 * 获取模版列表
	 *
	 * @param bool $short_mode
	 *
	 * @return array
	 */
	public function getTemplatesList( bool $short_mode = false ): array {
		$templates = parent::getTemplatesList( $short_mode );
		apply_filters( 'moredeal_block_templates', $templates );

		return $templates;
	}

	/**
	 * 渲染模版
	 *
	 * @param $view_name
	 * @param array $_data
	 *
	 * @return string
	 */
	public function render( $view_name, array $_data = array() ): string {
		return parent::render( $view_name, $_data );
	}

	public function getPartialViewPath( $view_name, $block = false ) {

		$file = parent::getPartialViewPath( $view_name, $block );
		if ( $file ) {
			return $file;
		}

		// allow render general block templates as partial
		$file = $this->getViewPath( $view_name );
		if ( $file ) {
			return $file;
		} else {
			return false;
		}
	}

}
