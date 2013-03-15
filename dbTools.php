<?php
class dbTools extends Database {
	
	function optimize( $table = '' ) {
		
		$tables = $this->listTables($table)->run();
		foreach($tables as $eachTable) {
			$this->command = 'optimize';
			$this->prepare( 'OPTIMIZE TABLE '.$eachTable )->run();
		}
		return $this;
		
	}
	
	function repair( $table = '' ) {
		
		$tables = $this->listTables($table)->run();
		foreach( $tables as $eachTable)	{
			$this->command = 'repair';
			$this->prepare( 'REPAIR TABLE '.$eachTable )->run();
		}
		return $this;
		
	}
	
	
	public function listTables ( $table = '' ) {
		
		$query_str = 'SHOW TABLES';
		$query_str .= ( $table == '' ) ? ' LIKE '.$table:'';	
		$this->command = 'find';
		$this->prepare($query_str)->run();
		return $this;
		
	}
	
	
	public function listFields ( $table ) {
		
		$query_str = 'SHOW COLUMNS FROM '.$table;	
		$this->command = 'find';
		$this->prepare($query_str)->run();
		return $this;
		
	}
	
}
?>