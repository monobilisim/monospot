<?php
function searchDir($base_dir="./",$p="",$f="",$allowed_depth=-1){
	$contents=array();

	$base_dir=trim($base_dir);
	$p=trim($p);
	$f=trim($f);

	if($base_dir=="")$base_dir="./";
	if(substr($base_dir,-1)!="/")$base_dir.="/";
	$p=str_replace(array("../","./"),"",trim($p,"./"));
	$p=$base_dir.$p;
	
	if(!is_dir($p))$p=dirname($p);
	if(substr($p,-1)!="/")$p.="/";

	if($allowed_depth>-1){
		$allowed_depth=count(explode("/",$base_dir))+ $allowed_depth-1;
		$p=implode("/",array_slice(explode("/",$p),0,$allowed_depth));
		if(substr($p,-1)!="/")$p.="/";
	}

	$filter=($f=="")?array():explode(",",strtolower($f));

	$files=@scandir($p);
	if(!$files)return array("contents"=>array(),"currentPath"=>$p);

	for ($i=0;$i<count($files);$i++){
		$fName=$files[$i];
		$fPath=$p.$fName;

		$isDir=is_dir($fPath);
		$add=false;
		$fType="folder";
		$fSize="";
		
		if(!$isDir){
			$ft=strtolower(substr($files[$i],strrpos($files[$i],".")+1));
			$fType=$ft;
			$fSize=Size(filesize($fPath));
			if($f!=""){
				if(in_array($ft,$filter))$add=true;
			}else{
				$add=true;
			}
		}else{
			if($fName==".")continue;
			$add=true;
			
			if($f!=""){
				if(!in_array($fType,$filter))$add=false;
			}

			if($fName==".."){
				if($p==$base_dir){
					$add=false;
				}else $add=true;
				
				$tempar=explode("/",$fPath);
				array_splice($tempar,-2);
				$fPath=implode("/",$tempar);
				if(strlen($fPath)<= strlen($base_dir))$fPath="";
			}
		}

		if($fPath!="")$fPath=substr($fPath,strlen($base_dir));
		if($add)$contents[]=array("fPath"=>$fPath,"fName"=>$fName,"fType"=>$fType,"fSize"=>$fSize);
	}
	
	$p=(strlen($p)<= strlen($base_dir))?$p="":substr($p,strlen($base_dir));
	return array("contents"=>$contents,"currentPath"=>$p);
}

$p=isset($_POST["path"])?$_POST["path"]:"";
$f=isset($_POST["filter"])?$_POST["filter"]:"";
//echo json_encode(searchDir("./",$p,$f,-1));
echo json_encode(searchDir("/logimza",$p,$f,-1));

function json_encode($data) {
	switch ($type = gettype($data)) {
		case 'NULL':
			return 'null';
		case 'boolean':
			return ($data ? 'true' : 'false');
		case 'integer':
		case 'double':
		case 'float':
			return $data;
		case 'string':
			return '"' . addslashes($data) . '"';
		case 'object':
			$data = get_object_vars($data);
		case 'array':
			$output_index_count = 0;
			$output_indexed = array();
			$output_associative = array();
			foreach ($data as $key => $value) {
				$output_indexed[] = json_encode($value);
				$output_associative[] = json_encode($key) . ':' . json_encode($value);
				if ($output_index_count !== NULL && $output_index_count++ !== $key) {
					$output_index_count = NULL;
				}
			}
			if ($output_index_count !== NULL) {
				return '[' . implode(',', $output_indexed) . ']';
			} else {
				return '{' . implode(',', $output_associative) . '}';
			}
		default:
			return ''; // Not supported
	}
}

function Size($bytes)
{
    if ($bytes > 0)
    {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB');

        if (array_key_exists($unit, $units) === true)
        {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}

?>