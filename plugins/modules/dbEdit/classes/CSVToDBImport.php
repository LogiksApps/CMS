<?php
if(!defined('ROOT')) exit('No direct script access allowed');

class CSVToDBImport {
	/*
	 * @file csv file to be imported
	 * @unique_col primary key of the table
	 * @headers Array that contains the header of csv file /column names
	 * 
	 **/
	
	public static function importCSV($table,$file,$unique_col='id') {
		if($file==null || strlen($file)==0 || !file_exists($file)) return array("error"=>"File Does Not Exist");
		if(!is_readable($file)) return array("error"=>"File Is Not Readable");
		
		$csvImport=new CSVToDBImport();
		$fp=fopen($file,'r');
		
		$headers = fgetcsv($fp, 2048, ',');
		$no_headers=sizeof($headers);
		if($unique_col!=null && strlen($unique_col)>0 && in_array($unique_col,$headers)){
			$index=array_search($unique_col,$headers);
			$sql="SELECT $unique_col from $table";	
			$res=_dbQuery($sql);
			if($res){
				$unique_data=array();
				while($rec=_db()->fetchData($res)){
					$unique_data[sizeof($unique_data)]=$rec[$unique_col];
				}
			}
			while($data = fgetcsv($fp, 2048, ',')) {
				if(sizeOf($data)<=0) continue;
				elseif(sizeOf($data)==1 && strlen($data[0])==0) continue;
				if(sizeof($data) != sizeof($headers)) {//if the no of columns are not equal to data count than this is not a proper csv data
					return array("error"=>"No of columns are not equal to data count than this is not a proper csv data");
				} else{
					if(!in_array($data[$index],$unique_data)) {
						$csvImport->insertCSVData($data,$headers,$table);
					} else{
						$csvImport->updateCSVData($data,$headers,$table,$index,$unique_col);				
					}
				}
			}
		} else {
			while($data = fgetcsv($fp, 2048, ',')){
				if(sizeof($data) != sizeof($headers)) {
					return array("error"=>"No of columns are not equal to data count than this is not a proper csv data");
				} else {
					$csvImport->insertCSVData($data,$headers,$table);
				}
			}
		}
		fclose($fp);
		return true;
	}
	private function insertCSVData($data,$headers,$table) {
		$str=implode(",",$headers);
		$sql="INSERT INTO $table ($str) VALUES(";
		foreach($data as $k=>$v){
			$sql .="'".mysql_real_escape_string($v)."',";					
		}
		$sql=substr($sql,0,strlen($sql)-1);
		$sql .=");";
		//echo $sql."<br>";
		_dbQuery($sql);
	}
	private function updateCSVData($data,$headers,$table,$index,$idCol){
		$sql="UPDATE $table SET ";
		for($j=0;$j<sizeof($data);$j++){
			if($headers[$j] !='id'){
				$sql .=$headers[$j] ."= '".mysql_real_escape_string($data[$j])."',";						
			}															
		}
		$sql=substr($sql,0,strlen($sql)-1);
		$sql .=" WHERE $idCol ='".$data[$index]."';";
		//echo $sql."<br>";
		_dbQuery($sql);
	}
}
?>