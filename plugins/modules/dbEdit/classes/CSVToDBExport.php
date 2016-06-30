<?php
if(!defined('ROOT')) exit('No direct script access allowed');

class CSVToDBExport {
	private static $SPECIAL_COLS=array("userid","privilegeid","scanBy","submittedby","createdBy","site","doc","doe","toc","toe","dtoc","dtoe","last_modified");
	/* This function is used to dump mysql table data in csv format
	 * @table the table whose data is to be dumped in a csv file
	 * */
	public static function export($table,$delimiter=",") {
		$sql="select * from $table";
		$maxReset=100;
		$result=_dbQuery($sql);
		if($result){
			$num_rows=_db()->recordCount($result);
			$num_fields=_db()->columnCount($result);
			
			$i=0;
			while($i<$num_fields){
				$meta=_db()->fetchField($result,$i);
				echo $meta->name;
				if($i<$num_fields-1){
					echo $delimiter;
				}
				$i++;
			}
			echo "\n\n";
			$cnt=0;
			if($num_rows>0){
				while($row=mysql_fetch_array($result)){
					for($i=0;$i<$num_fields;$i++){
						echo mysql_real_escape_string($row[$i]);
						if($i<$num_fields-1){
							echo $delimiter;
						}
					}
					echo "\n";
				}
				$cnt++;
				if($cnt==$maxReset) {
					$cnt=0;
					ob_flush();
				}
			}
			_dbFree($result);	
		}	
	}
	public static function exportHeader($table,$delimiter=",") {
		$sql="select * from $table limit 1";
		$result=_dbQuery($sql);
		if($result) {
			$num_fields=_db()->columnCount($result);
			
			$i=0;
			$s="";
			while($i<$num_fields) {
				$meta=_db()->fetchField($result,$i);
				if(!in_array($meta->name,CSVToDBExport::$SPECIAL_COLS)) {
					$s.=$meta->name;
					if($i<$num_fields-1){
						$s.=$delimiter;
					}
				}
				$i++;
			}
			_dbFree($result);
			$s=trim($s);
			if(substr($s,strlen($s)-1)==",") $s=substr($s,0,strlen($s)-1);
			echo "{$s}\n\n";
		}
	}
}
?>