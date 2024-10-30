<?php

namespace Moredeal\application\components;

defined('\ABSPATH') || exit;

use Moredeal\application\helpers\TextHelper;
use Moredeal\application\Plugin;

abstract class TemplateManager {

	/**
	 * @var array|null 模版包名称
	 */
	private ?array $templates = null;

	/**
	 * @var array
	 */
	private array $last_render_data = array();

	/**
	 * 获取模版路径前缀
	 * @return string
	 */
	abstract public function getTemplatePrefix(): string;

	/**
	 * 获取模版路径
	 * @return string
	 */
	abstract public function getTemplateDir(): string;

	/**
	 * 自定义模版路径
	 * @return array
	 */
	abstract public function getCustomTemplateDirs(): array;

	/**
	 * 获取模版集合
	 *
	 * @param bool $short_mode
	 *
	 * @return array|string|null
	 */
	public function getTemplatesList( bool $short_mode = false ) {
		$prefix = $this->getTemplatePrefix();
		if ( $this->templates === null ) {
			$templates = array();
			foreach ( $this->getCustomTemplateDirs() as $custom_name => $dir ) {
				$templates = array_merge( $templates, $this->scanTemplates( $dir, $prefix, $custom_name ) );
			}
			$templates       = array_merge( $templates, $this->scanTemplates( $this->getTemplateDir(), $prefix, false ) );
			$this->templates = $templates;
		}

		if ( $short_mode ) {
			$list = array();
			foreach ( $this->templates as $id => $name ) {
				$custom = '';
				if ( self::isCustomTemplate( $id ) ) {
					$parts  = explode( '/', $id );
					$custom = 'custom/';
					$id     = $parts[1];
				}
				// del prefix
				$list[ $custom . substr( $id, strlen( $prefix ) ) ] = $name;
			}

			return $list;
		}

		return $this->templates;
	}

	/**
	 * 扫描模版
	 *
	 * @param string $path
	 * @param string $prefix
	 * @param bool $custom_name
	 *
	 * @return array
	 */
	private function scanTemplates( string $path, string $prefix, bool $custom_name = false ): array {

		if ( $custom_name && ! is_dir( $path ) ) {
			return array();
		}

		$tpl_files = glob( $path . '/' . $prefix . '*.php' );
		if ( ! $tpl_files ) {
			return array();
		}

		$templates = array();
		foreach ( $tpl_files as $file ) {
			$template_id = basename( $file, '.php' );
			if ( $custom_name ) {
				$template_id = 'custom/' . $template_id;
			}

			$data = get_file_data( $file, array( 'name' => 'Name' ) );
			if ( $data && ! empty( $data['name'] ) ) {
				$templates[ $template_id ] = sanitize_text_field( $data['name'] );
			} else {
				$templates[ $template_id ] = $template_id;
			}

			if ( $custom_name ) {
				$templates[ $template_id ] .= ' [' . esc_attr( __( $custom_name, 'moredeal' ) ) . ']';
			}

		}

		return $templates;
	}

	/**
	 * 渲染
	 *
	 * @param $view_name
	 * @param array $_data
	 *
	 * @return false|string
	 */
	public function render( $view_name, array $_data = array() ) {
		$file = $this->getViewPath( $view_name );
		if ( ! $file ) {
			return '';
		}

		$this->last_render_data = $_data;
		extract( $_data, EXTR_PREFIX_SAME, 'data' );
		ob_start();
		ob_implicit_flush( false );
		include $file;
		return ob_get_clean();
	}

	/**
	 * 部分渲染
	 *
	 * @param $view_name
	 * @param array $_data
	 *
	 * @return string|void
	 * @throws \Exception
	 */
	public function renderPartial( $view_name, array $_data = array() ) {
		$file = $this->getPartialViewPath( $view_name, false );
		if ( ! $file ) {
			return '';
		}
		$this->renderPath( $file, $_data );
	}

	/**
	 * 渲染 block
	 *
	 * @param $view_name
	 * @param array $data
	 *
	 * @return string|void
	 * @throws \Exception
	 */
	public function renderBlock( $view_name, array $data = array() ) {
		$file = $this->getPartialViewPath( $view_name, true );
		if ( ! $file ) {
			return '';
		}
		$this->renderPath( $file, $data );
	}

	/**
	 * 渲染路径
	 *
	 * @param $view_path
	 * @param array $_data
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function renderPath( $view_path, array $_data = array() ) {
		if ( ! is_file( $view_path ) || ! is_readable( $view_path ) ) {
			throw new \Exception( 'View file "' . $view_path . '" does not exist.' );
		}

		$_data = array_merge( $this->last_render_data, $_data );
		extract( $_data, EXTR_PREFIX_SAME, 'data' );
		include $view_path;
	}

	/**
	 * 获取模版路径
	 *
	 * @param $view_name
	 * @param bool $block
	 *
	 * @return false|string
	 */
	public function getPartialViewPath( $view_name, bool $block = false ) {
		$view_name = str_replace( '.', '', $view_name );
		$file      = \Moredeal\PLUGIN_PATH . 'application/templates/';
		if ( $block ) {
			$file .= 'blocks/';
		} else {
			$file .= $this->getTemplatePrefix();
		}
		$file .= TextHelper::clear( $view_name ) . '.php';
		if ( is_file( $file ) && is_readable( $file ) ) {
			return $file;
		} else {
			return false;
		}
	}

	/**
	 * 获取模版路径
	 *
	 * @param $view_name
	 *
	 * @return false|string
	 */
	public function getViewPath( $view_name ) {
		$view_name = str_replace( '.', '', $view_name );
		if ( self::isCustomTemplate( $view_name ) ) {

			$view_name = substr( $view_name, 7 );
			foreach ( $this->getCustomTemplateDirs() as $custom_prefix => $custom_dir ) {
				$tpl_path = $custom_dir;
				$file     = $tpl_path . DIRECTORY_SEPARATOR . TextHelper::clear( $view_name ) . '.php';
				if ( is_file( $file ) && is_readable( $file ) ) {
					return $file;
				}
			}

			return false;
		} else {
			$tpl_path = $this->getTemplateDir();
			$file     = $tpl_path . DIRECTORY_SEPARATOR . TextHelper::clear( $view_name ) . '.php';
			if ( is_file( $file ) && is_readable( $file ) ) {
				return $file;
			} else {
				return false;
			}
		}
	}


	/**
	 * 是否是自定义模版
	 *
	 * @param $template_id
	 *
	 * @return bool
	 */
	public static function isCustomTemplate( $template_id ): bool {
		if ( substr( $template_id, 0, 7 ) == 'custom/' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 模版是否存在
	 *
	 * @param $tpl
	 *
	 * @return bool
	 */
	public function isTemplateExists( $template ): bool {
		$templates_list = $this->getTemplatesList();

		return array_key_exists( $template, $templates_list );
	}

	/**
	 * 获取模版目录
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function prepareShortcodeTemplate( $template ): string {
		if ( self::isCustomTemplate( $template ) ) {
			$is_custom = true;
			// del 'custom/' prefix
			$template = substr( $template, 7 );
		} else {
			$is_custom = false;
		}

		$template = TextHelper::clear( $template );
		if ( $is_custom ) {
			$template = 'custom/' . $template;
		}
		if ( $template ) {
			$template = $this->getFullTemplateId( $template );
		}

		return $template;
	}

	/**
	 * 获取全部模版名称
	 *
	 * @param $short_id
	 *
	 * @return string
	 */
	public function getFullTemplateId( $short_id ): string {
		$prefix = $this->getTemplatePrefix();
		$custom = '';
		if ( self::isCustomTemplate( $short_id ) ) {
			$parts  = explode( '/', $short_id );
			$custom = 'custom/';
			$id     = $parts[1];
		} else {
			$id = $short_id;
		}

		// check _data prefix
		if ( substr( $id, 0, strlen( $prefix ) ) != $prefix ) {
			$id = $prefix . $id;
		}

		return $custom . $id;
	}

}