<?php

namespace Moredeal\application\components;
use LicenseInfoTable;
use Moredeal\application\admin\LicenseConfig;

defined( '\ABSPATH' ) || exit;

class LicenseManager {

	const CACHE_TTL = 1;

	const LICENSE_INFO = 'moredeal_license_info';

	private $data = null;

	private static ?LicenseManager $instance = null;

	/**
	 * 获取实例
	 * @return LicenseManager
	 */
	public static function getInstance(): ?LicenseManager {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function adminInit() {
		add_action( 'admin_notices', array( $this, 'displayNotice' ) );
	}

	/**
	 * 获取License信息
	 *
	 * @param bool $force
	 *
	 * @return array
	 */
	public function getData( bool $force = false ): ?array {
		$license = LicenseConfig::getInstance()->option( 'license_key' );
		if ( ! $license ) {
			return array();
		}

//		if ( ! $force && $this->data !== null ) {
//			return $this->data;
//		}
//
//		$this->data = $this->getCache();
//		if ( $this->data === false || $force ) {
//
//			$data = $this->getLicenseInfo( $license );
//			if ( ! $data || ! is_array( $data ) ) {
//				$data = array();
//			}
//
//			$this->data = $data;
//			$this->saveCache( $this->data );
//		}
		$data = $this->getLicenseInfo( $license );
		if ( ! $data || ! is_array( $data ) ) {
			$data = array();
		}

		$this->data = $data;
		//$this->saveCache( $this->data );

		return $this->data;
	}

	/**
	 * 请求接口获取License信息
	 *
	 * @param $license
	 *
	 * @return array|false
	 */
	public function getLicenseInfo( $license ) {
		$result = SearchProductClient::getInstance()->activeInfo( $license );
		if ( $result == null ) {
			return false;
		}
		$result = (array) $result;
		if ( array_key_exists( 'code', $result ) && $result['code'] != 200 ) {
			return false;
		}
		if ( array_key_exists( 'success', $result ) && $result['success'] ) {
			if ( array_key_exists( 'data', $result ) && $result['data'] ) {
				return (array) $result['data'];
			}

			return false;
		}

		return false;
	}

	/**
	 * 保存License 缓存信息
	 *
	 * @param $data
	 *
	 * @return void
	 */
	public function saveCache( $data ) {
		set_transient( self::LICENSE_INFO, $data, self::CACHE_TTL );
	}

	/**
	 * 获取License 缓存信息
	 * @return mixed
	 */
	public function getCache() {
		return get_transient( self::LICENSE_INFO );
	}

	/**
	 * 删除License 缓存信息
	 * @return void
	 */
	public function deleteCache() {
		delete_transient( self::LICENSE_INFO );
	}

	/**
	 * 删除License 缓存信息
	 * @return void
	 */
	public function clearData() {
		$this->data = null;
	}

	/**
	 * 是否是 license 配置页面
	 * @return bool
	 */
	public function isConfigPage(): bool {
		if ( $GLOBALS['pagenow'] == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'moredeal-license' ) {
			return true;
		} else {
			return false;
		}
	}

	public function displayNotice() {

		if ( ! $data = self::getInstance()->getData() ) {
			return;
		}

		if ( $this->isConfigPage() ) {
			$this->displayActiveNotice( $data );
		}
	}

	public function displayActiveNotice( array $data ) {
		$this->addInlineCss();
		$license      = $data['license'] ?? LicenseConfig::getInstance()->option( 'license_key' );
		$days_left    = floor( ( strtotime( $data['expireTime'] ) - time() ) / 3600 / 24 );
		$status       = $this->getStatus( strtoupper( $data['codeStatus'] ) );
		$extData      = (array) $data['extendData'] ?? array();
		$limitBind    = array_key_exists( 'limitBind', $extData ) ? $extData['limitBind'] : null;
//		$limitRequest = array_key_exists( 'limitRequest', $extData ) ? $extData['limitRequest'] : null;
//		$bindDomains  = array_key_exists( 'bindSourceMarkList', $data ) ? (array) $data['bindSourceMarkList'] : array();
		if ( strtoupper( $status ) == 'ACTIVE' ) {
			$color = '#00ba37';
		} else {
			$color = '#d63638';
		}
		echo '<div class="notice notice-success moredeal-notice"><p>'.__('License status: ', 'moredeal').'<span class="moredeal-label moredeal-label-active" style="background-color: ' . $color . '">';
		echo __( strtoupper( $status ), 'moredeal' );
		echo ' </span> &nbsp;&nbsp;';
		if ( strtoupper( $status ) == 'NOACTIVE' ) {
			echo ' ' . __( 'The License has no active, place contact your administrator', 'moredeal' );
		}
		if ( strtoupper( $status ) == 'ACTIVE' ) {
			echo ' ' . __( 'You can use the advanced features of Moredeal', 'moredeal' );
		}
		if ( strtoupper( $status ) == 'INACTIVE' ) {
			echo ' ' . __( 'The License has expired, please obtain a new license key', 'moredeal' );
		}
		if ( strtoupper( $status ) == 'DISABLE' ) {
			echo ' ' . __( 'The License has disabled, place contact your administrator', 'moredeal' );
		}

		echo '<br />' . sprintf( __( 'Active at %s ', 'moredeal' ), gmdate( 'Y-m-d H:i', strtotime( $data['activeTime'] ) ) );;
		echo '<br />' . sprintf( __( 'Expires at %s (%d days left)', 'moredeal' ), gmdate( 'Y-m-d H:i', strtotime( $data['expireTime'] ) ), strval( $days_left ) );
		echo '</p>';

		if ( $limitBind ) {
			echo '<p>' . sprintf( __( 'This License code can be bind to %s websites', 'moredeal' ), $limitBind );
		}
		$sources = $data['sources'] ?? array();
		$rows    = array( 'bind_domain', 'queries', 'options' );
		if ( count( $sources ) > 0 ) {
			echo ' ' .sprintf(__( '. Already bind websites: %s', 'moredeal' ), count($sources));
			if (count($sources) < $limitBind) {
				echo ' ' . sprintf( __( '. You can bind %s websites', 'moredeal' ), ($limitBind - count($sources)) );
			} else {
				echo ' ' . __( '. You can not bind website, if you want bind other website, you can unbind already bind websites', 'moredeal' );
			}
			echo '</p>';
			echo '<div class="products">';
			echo '<div class="comp_wrapper">';
			foreach ( $rows as $index => $row ) {
				echo '<div class="comp_row">';
				echo '<div class="comp_thead">' . esc_html( __($row, 'moredeal') );
				if ($row == 'queries') {
					echo '<i class="moredeal-ico-info-circle title-tip" data-title="' . __( "The number of queries allowed per day", "moredeal" ) . '" ></i>';
				}
				echo '</div>';
				$this->displayDomain( $sources, $row );
				$this->displayQueries( $sources, $row );
				$this->displayUnbind( $sources, $row, $license );
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}

	public function addInlineCss() {
		$url = "http://www.w3.org/2000/svg' width='16' height='16' fill='%23777' viewBox='0 0 16 16'%3E%3Cpath d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/%3E%3Cpath d='m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z";
		echo '<style>.moredeal-notice a.moredeal-notice-close {position:static;font-size:13px;float:right;top:0;right0;padding:0;margin-top:-20px;line-height:1.23076923;text-decoration:none;}.moredeal-notice a.moredeal-notice-close::before{position: relative;top: 18px;left: -20px;}.moredeal-notice img {float:left;width:40px;padding-right:12px;}</style>';
		echo '<style>.comp_wrapper{display:table;font-size:13px;color:#7A7A7A;border:1px solid #ececec;margin-bottom:8px}.comp_wrapper .comp_row:nth-child(2n+1){background-color:#f5f5f5}.comp_row{display:table-row}.comp_thead{font-weight:600;max-width:140px}.com_data,.comp_thead{width:140px;display:table-cell;vertical-align:middle;position:relative;text-align:center;padding:10px 10px 10px 10px;}.comp_wrapper .comp_row:nth-child(1) > div{padding:10px 5px}.comp_wrapper a{overflow:hidden;display:block;text-align:center;box-shadow:none;text-decoration:none;color:#448FD5;font-weight:700} </style>';
		echo '<style>.title-tip:hover::after{color:#0c0c0c;background:#ececec;font-size:12px;line-height:15px;text-align:left;width:230px;font-style:normal;content:attr(data-title);padding:6px 6px 6px 6px;border:1px solid #ddd;border-radius:3px;position:absolute;top:50px;right:0}</style>';
	}

	public function getStatus( $status ) {
		if ( $status == 'NOACTIVE' ) {
			return 'noactive';
		} else if ( $status == 'ACTIVE' ) {
			return 'active';
		} else if ( $status == 'EFFICACY' ) {
			return 'inactive';
		} else if ( $status == 'DISABLE' ) {
			return 'disable';
		}
	}

	/**
	 * 渲染域名
	 *
	 * @param $data
	 * @param $row
	 *
	 * @return void
	 */
	public function displayDomain( $data, $row ) {
		if ( $row != 'bind_domain' ) {
			return;
		}
		foreach ( $data as $item ) {
			$item = (array) $item;
			if ( $item['sourceMark'] ) {
				$color = '#448FD5';
				echo '<div class="com_data data_product" >';
				if ( $item['sourceMark'] == SearchProductClient::getCurrDomain()) {
					echo '<span class="moredeal-label moredeal-label-active" style="background-color: #00ba37">' . __( 'Current Website', 'moredeal' ) . '</span>';
				}
				echo '<a target="_blank" style="color: ' . $color . '" href="' . esc_url_raw( $item['sourceMark'] ) . '">' . esc_html( $item['sourceMark'] ) . '</a>';

				echo '</div>';
			}
		}
	}

	/**
	 * 渲染商品查询次数
	 *
	 * @param $data
	 * @param $row
	 *
	 * @return void
	 */
	public function displayQueries( $data, $row ) {
		if ( $row != 'queries' ) {
			return;
		}
		foreach ( $data as $item ) {
			$item = (array) $item;
			if (array_key_exists('extendData', $item) && $item['extendData']) {
				$extendData = (array)$item['extendData'];
				if (array_key_exists('limitRequest', $extendData) && $extendData['limitRequest']) {
					$limit = $extendData['limitRequest'];
					if ( $limit == - 1 ) {
						echo '<div class="com_data data_product" >';
						echo '<span>' . esc_html( __( 'Unlimited', 'moredeal' ) ) . '</span>';
						echo '</div>';
					} else {
						if ( array_key_exists( 'unitTime', $extendData ) && $extendData['unitTime'] ) {
							$unitTime = $extendData['unitTime'];
						} else {
							$unitTime = 'MONTH';
						}
						$unit = $this->getTimeUnit( $unitTime );
						echo '<div class="com_data data_product" >';
						echo '<span>' . esc_html( $limit ) . esc_html(__(' times / ', 'moredeal')) . esc_html( $unit ) . '</span>';
						echo '</div>';
					}
				}
			}
		}
	}

	public function getTimeUnit( $unitTime ) {
		if ( $unitTime == 'DAY' ) {
			return __( 'Day', 'moredeal' );
		} elseif ( $unitTime == 'WEEK' ) {
			return __( 'Week', 'moredeal' );
		} elseif ( $unitTime == 'MONTH' ) {
			return __( 'Month', 'moredeal' );
		} elseif ( $unitTime == 'YEAR' ) {
			return __( 'Year', 'moredeal' );
		}

		return __( 'Month', 'moredeal' );
	}

	/**
	 * 渲染解绑
	 *
	 * @param $data
	 * @param $row
	 * @param $license
	 *
	 * @return void
	 */
	public function displayUnbind( $data, $row, $license ) {
		if ( $row != 'options' ) {
			return;
		}
		foreach ( $data as $index => $item ) {
			$item = (array) $item;
			if ( $item['sourceMark'] ) {
				echo '<div class="com_data data_product" >';
				echo '<form action=" ' . esc_url_raw( get_admin_url( get_current_blog_id(), 'admin.php?page=moredeal-license' ) ) . ' " method="POST">';
				echo '<input type="hidden" name="license" id="license_' . $index . '" value="' . esc_attr( $license ) . '"/>';
				echo '<input type="hidden" name="domain" id="domain_' . $index . '" value="' . esc_attr( $item['sourceMark'] ) . '"/>';
				echo '<input type="hidden" name="license_unbind_tag" id="license_unbind_tag_' . $index . '" value="unbind"/>';
				echo '<input type="submit" style="border:none;background:#f5f5f5;color:#448FD5;font-weight:700;cursor:pointer" name="submit_unbind_' . $index . '" id="submit_unbind_' . $index . '" value="' . __( 'Unbind', 'moredeal' ) . '"/>';
				echo '</form>';
				echo '</div>';
			}
		}
	}


}