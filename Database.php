<?
class Database {

	public $db = false;
	public $query_str;
	public $query;
	public $query_args;
	public $values;
	public $command;
	public $table;
	public $result;
	public $index;

	public function __construct($c) {
		
		$type = (isset($c['type'])) ? $c['type']:'mysql';
		$this->db = new PDO( 
			$type.':host='.$c['host'].';dbname='.$c['dbname'], 
			$c['user'], 
			$c['password'] 
		);
		return $this;
		
	} // end __construct
	
	
	public function __call($name, $arguments) { // Expects: find, change, delete, or create 
	
		$this->command = $name;
		$this->query_args = $arguments[0];
		$this->query();
		return $this;
		
	} // end __call
	
	
	public function prepare($query) {
		$db = $this->db;
		$this->command = 'find';
		$query = $db->prepare( $query );	
	}
	
	public function query() {
		
		$default_query = array(
			'return' => '*',
			'order'  => 'id',
			'limit'  => '0,30',
			'sort'   => 'DESC'
		);
		
		$query = array_merge( $default_query, $this->query_args );
		
		extract( $query );
	
		$command = array(
			'delete' => 'DELETE FROM `'.$this->table.'` WHERE 1',
			'change' => 'UPDATE `'.$this->table.'`',
			'create' => 'INSERT INTO `'.$this->table.'`',
			'find'   => 'SELECT '.$return.' FROM `'.$this->table.'` WHERE 1'
		);
		
		$this->query_str = $command[$this->command];
		
		if ( isset( $values ) ) {
			foreach ( $values as $key => $value ) :
				$update .= ' '.$key.' = ":update'.$key.'",';
			endforeach;
			$this->query_str .= ' SET'.$update.' WHERE 1';
		}

		$operator = array( 'eq' => '=', 'gt' => '>', 'lt' => '<', 'gte' => '>=', 'lte' => '<=', 'like' => 'LIKE' );
		
		foreach ( $operator as $group => $condition ) :
			if ( isset( ${$group} ) ) {
				foreach ( ${$group} as $name => $value ) :
					$this->query_str .= ' AND '.$name.' '.$condition.' :'.$name;
				endforeach;
			}
		endforeach;
			
		if ( $this->command == 'find' ) {
			$this->query_str .= ' ORDER BY '.$order.' '.$sort.' LIMIT '.$limit;
		}
			
		$db = $this->db;
		$query = $db->prepare( $this->query_str );

		if( isset($values) ) :
			foreach ( $values as $key => $value ) :
				$query->bindValue( ':update'.$key, $value );
			endforeach;
		endif;
		
		foreach ($operator as $group => $value) :
			if ( isset( ${$group} ) ) {
				foreach ( ${$group} as $name => $condition ) :
					$query->bindValue( ':'.$name, $condition );
				endforeach;
			}
		endforeach;
		
		$this->query = $query;
		
		return $this;

	} // end query
	
	
	public function setTable( $table ) {
		$this->table = $table;
		return $this;	
	} // end setTable
	
	
	public function first() {
		
		$results = (array) $this->result;
		$this->index = 1;
		return $results[0];
		
	}
	
	
	public function record($index) {
		
		$results = (array) $this->result;
		$this->index++;
		return $results[$index];
		
	}
	
	
	public function next() {
		
		$results = (array) $this->result;
		$index = $this->index;
		$this->index++;
		return $results[$index];
		
	}
	
	
	public function prev() {
		
		$results = (array) $this->result;
		$this->index--;
		$index = $this->index;
		$this->index++;
		return $results[$index];
		
	}
	
	
	public function last() {
		
		$results = (array) $this->result;
		$record = array_pop($results);
		return $record;
		
	}
		
		
	public function run( $format = 'array' ) { 
	
		$query = $this->query;
		$success = $query->execute();
		
		if ( $this->command == 'find' AND $success == true ) {	
			if ( $format == 'array' ) {
				$result = $query->fetchAll();
			} elseif ( $format == 'object' ) {
				$result = $query->fetchAll( PDO::FETCH_OBJ );
			} elseif ( $format == 'json' ) {
				$result = json_encode( $query->fetchAll() );
			}
			$this->result = $result;
			return $result;
		} else {
			return $success;
		}
		
	} // end run
	
}
?>