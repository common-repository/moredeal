<?php

namespace Moredeal\application\models;

defined( '\ABSPATH' ) || exit;

/**
 * Model class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
abstract class Model {

	/**
	 * db
	 */
	public static $db;


	private static array $models = array();


	protected string $charset_collate = '';

	/**
	 * tableName
	 *
	 * @return string
	 */
	abstract public function tableName(): string;

	/**
	 * getDump
	 *
	 * @return string
	 */
	abstract public function getDump(): string;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! empty( $this->getDb()->charset ) ) {
			$this->charset_collate = 'DEFAULT CHARACTER SET ' . $this->getDb()->charset;
		}
		if ( ! empty( $this->getDb()->collate ) ) {
			$this->charset_collate .= ' COLLATE ' . $this->getDb()->collate;
		}
		if ( ! $this->charset_collate ) {
			$this->charset_collate = '';
		}
	}

	/**
	 * attributeLabels
	 *
	 * @return array
	 */
	public function attributeLabels(): array {
		return array();
	}

	/**
	 * getDb
	 *
	 * @return \wpdb
	 */
	public function getDb(): \wpdb {
		if ( self::$db !== null ) {
			return self::$db;
		} else {
			self::$db = $GLOBALS['wpdb'];

			return self::$db;
		}
	}

	/**
	 * model
	 *
	 * @param mixed $className
	 */
	public static function model( $className = __CLASS__ ) {
		if ( isset( self::$models[ $className ] ) ) {
			return self::$models[ $className ];
		} else {
			return self::$models[ $className ] = new $className;
		}
	}

	/**
	 * getAttributeLabel
	 */
	public function getAttributeLabel( $attribute ) {
		$labels = $this->attributeLabels();
		if ( isset( $labels[ $attribute ] ) ) {
			return $labels[ $attribute ];
		} else {
			return $this->generateAttributeLabel( $attribute );
		}
	}

	/**
	 * generateAttributeLabel
	 */
	public function generateAttributeLabel( $name ) {
		return ucwords( trim( strtolower( str_replace( array(
			'-',
			'_',
			'.'
		), ' ', preg_replace( '/(?<![A-Z])[A-Z]/', ' \0', $name ) ) ) ) );
	}

	/**
	 * getTableSchema
	 */
	public function find( array $params ) {
		return $this->getDb()->get_row( $this->prepareFindSql( $params ), \ARRAY_A );
	}

	/**
	 * findAll
	 */
	public function findAll( array $params ) {
		return $this->getDb()->get_results( $this->prepareFindSql( $params ), \ARRAY_A );
	}

	/**
	 * prepareFindSql
	 */
	private function prepareFindSql( array $params ): ?string {
		$values = array();
		$sql    = 'SELECT ';

		if ( ! empty( $params['select'] ) ) {
			$sql .= $params['select'];
		} else {
			$sql .= ' *';
		}
		$sql .= ' FROM ' . $this->tableName();
		if ( $params ) {
			if ( ! empty( $params['where'] ) ) {
				if ( is_array( $params['where'] ) && isset( $params['where'][0] ) && isset( $params['where'][1] ) ) {
					$sql    .= ' WHERE ' . $params['where'][0];
					$values += $params['where'][1];
				} elseif ( ! is_array( $params['where'] ) ) {
					$sql .= ' WHERE ' . $params['where'];
				}
			}
			if ( ! empty( $params['group'] ) ) {
				$sql .= ' GROUP BY ' . $params['group'];
			}
			if ( ! empty( $params['order'] ) ) {
				$sql .= ' ORDER BY ' . $params['order'];
			}
			if ( ! empty( $params['limit'] ) ) {
				$sql      .= ' LIMIT %d';
				$values[] = $params['limit'];
			}
			if ( ! empty( $params['offset'] ) ) {
				$sql      .= ' OFFSET %d';
				$values[] = $params['offset'];
			}

			if ( $values ) {
				$sql = $this->getDb()->prepare( $sql, $values );
			}
		}

		return $sql;
	}

	/**
	 * count
	 */
	public function findByPk( $id ) {
		return $this->getDb()->get_row( $this->getDb()->prepare( 'SELECT * FROM ' . $this->tableName() . ' WHERE id = %d', $id ), ARRAY_A );
	}

	/**
	 * count
	 */
	public function delete( $id ) {
		return $this->getDb()->delete( $this->tableName(), array( 'id' => $id ), array( '%d' ) );
	}

	/**
	 * count
	 * @throws \Exception
	 */
	public function deleteAll( $where ) {
		$sql = 'DELETE FROM ' . $this->tableName();
		$sql .= $this->prepareWhere( $where );

		return $this->getDb()->query( $sql );
	}

	/**
	 * count
	 */
	public function count( $where = null ) {
		$sql = "SELECT COUNT(*) FROM " . $this->tableName();
		if ( $where ) {
			$sql .= $this->prepareWhere( $where );
		}

		return $this->getDb()->get_var( $sql );
	}

	/**
	 * prepareWhere
	 */
	public function max( $colum, $where = null ) {
		$sql = "SELECT MAX(" . $colum . ") FROM " . $this->tableName();
		if ( $where ) {
			$sql .= $this->prepareWhere( $where );
		}

		return $this->getDb()->get_var( $sql );
	}

	/**
	 * prepareWhere
	 * @throws \Exception
	 */
	public function min( $colum, $where = null ) {
		$sql = "SELECT MIN(" . $colum . ") FROM " . $this->tableName();
		if ( $where ) {
			$sql .= $this->prepareWhere( $where );
		}

		return $this->getDb()->get_var( $sql );
	}

	/**
	 * prepareWhere
	 * @throws \Exception
	 */
	public function avg( $colum, $where = null ) {
		$sql = "SELECT AVG(" . $colum . ") FROM " . $this->tableName();
		if ( $where ) {
			$sql .= $this->prepareWhere( $where );
		}

		return $this->getDb()->get_var( $sql );
	}

	/**
	 * prepareWhere
	 */
	public function prepareWhere( $where, $winclude = true ): ?string {
		if ( $winclude ) {
			$sql = ' WHERE ';
		} else {
			$sql = '';
		}
		$values = array();
		if ( is_array( $where ) && isset( $where[0] ) && isset( $where[1] ) ) {
			$sql    .= $where[0];
			$values += $where[1];
		} elseif ( is_string( $where ) ) {
			$sql .= $where;
		} else {
			throw new \Exception( 'Wrong WHERE params.' );
		}
		if ( $values ) {
			$sql = $this->getDb()->prepare( $sql, $values );
		}

		return $sql;
	}

	/**
	 * save
	 */
	public function save( array $item ): int {
		$item['id'] = (int) $item['id'];
		if ( ! $item['id'] ) {
			$item['id'] = null;
			$this->getDb()->insert( $this->tableName(), $item );

			return $this->getDb()->insert_id;
		} else {
			$this->getDb()->update( $this->tableName(), $item, array( 'id' => $item['id'] ) );

			return $item['id'];
		}
	}

	/**
	 * insert
	 */
	public function insert( array $item ) {
		return $this->getDb()->insert( $this->tableName(), $item );
	}

	/**
	 * cleanOld
	 */
	public function cleanOld( $days, $optimize = true, $date_field = 'create_date' ) {
		$this->deleteAll( 'TIMESTAMPDIFF( DAY, ' . $date_field . ', "' . \current_time( 'mysql' ) . '") > ' . $days );
		if ( $optimize ) {
			$this->optimizeTable();
		}
	}

	/**
	 * optimizeTable
	 */
	public function optimizeTable() {
		$this->getDb()->query( 'OPTIMIZE TABLE ' . $this->tableName() );
	}

	/**
	 * truncateTable
	 */
	public function truncateTable() {
		$this->getDb()->query( 'TRUNCATE TABLE ' . $this->tableName() );
	}

	/**
	 * isTableExists
	 */
	public function isTableExists(): bool {
		$query = $this->getDb()->prepare( 'SHOW TABLES LIKE %s', $this->getDb()->esc_like( $this->tableName() ) );

		if ( $this->getDb()->get_var( $query ) == $this->tableName() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * multipleInsert
	 */
	public function multipleInsert( array $items, $per_request = 200 ) {
		$fields      = array_keys( reset( $items ) );
		$sql         = 'INSERT INTO ' . $this->tableName() . ' (' . join( ',', $fields ) . ') VALUES ';
		$placeholder = str_repeat( '%s, ', count( $fields ) );
		$placeholder = '(' . rtrim( $placeholder, " ," ) . ')';

		$request_count = ceil( count( $items ) / $per_request );
		for ( $i = 0; $i < $request_count; $i ++ ) {
			$query = $sql;
			for ( $j = $i * $per_request; $j < $i * $per_request + $per_request; $j ++ ) {
				if ( ! isset( $items[ $j ] ) ) {
					break;
				}
				$query .= $this->getDb()->prepare( $placeholder, $items[ $j ] ) . ', ';
			}
			$query = rtrim( $query, " ," ) . ';';
			$this->getDb()->query( $query );
		}
	}

}
