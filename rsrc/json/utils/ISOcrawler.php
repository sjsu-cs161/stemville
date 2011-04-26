<?php

$ISOS = array();
$file_list = array();
$dir_path = "org/eclipse/stem/data/geography";
if ($dhandle = opendir($dir_path))
{
	$i = 0;
	while(false !== ($file = readdir($dhandle)))
	{
		if ($file == '.' || $file == '..') continue;
		if (! is_dir($file)) if (strlen($file) == 20)
		{
			$file_list[$i++] = $file;
		}
	}
	closedir($dhandle);
}
//asort($file_list);
//echo var_dump($file_list);
foreach($file_list as $key =>$file)
{
	if ($fhandle = fopen($dir_path."/".$file, "r"))
	{
		$level = 0;
		$l0 = false;
		$l1 = false;
		$l2 = false;
		//echo "opened file : ".$file."\n";
		while(($buffer = fgets($fhandle, 4096)) !== false)
		{
			$buffer = utf8_encode($buffer); //unlike most of eclipses files these are not encoded properly thanks IBM
			if($buffer{0} === "#")
				continue;
			$exploded = explode("=", $buffer."=");
			if (trim($exploded[0]) ==="") continue;
			if (strlen(trim($exploded[0])) > 6) $level = 2;
			if ($level == 0) 
			{
				//$l0 = true;
				//$l1 = false;
				//$l2 = false;
				$cc = trim($exploded[0]);
				$ISOS[$cc] = array();
				$ISOS[$cc][0] = trim($exploded[1]);
				$ISOS[$cc][1] = array();
				$ISOS[$cc][2] = array();
				$ISOS[$cc]['c'] = ""; 
				$level = 1;
				$c = 0;
				$d = 0;
				continue;
			}
			if ($level == 1)
			{
				$l1 = true;
				$ISOS[$cc][$level][$c] = array();
				$ISOS[$cc][$level][$c]["code"] = trim($exploded[0]);
				$ISOS[$cc][$level][$c++]["name"] = trim($exploded[1]);
				continue;
			}
			if ($level == 2)
			{
				$l2 = true;
				$ISOS[$cc][$level][$d] = array();
				$ISOS[$cc][$level][$d]["code"] = trim($exploded[0]);
				$ISOS[$cc][$level][$d++]["name"] = trim($exploded[1]);
				continue;
			}

		}
		if (!$l1)
		{
			$ISOS[$cc][1][$c] = array();
			//$ISOS[$cc][1][$c]["code"] = "N/A";
			//$ISOS[$cc][1][$c]["name"] = "No Level 1 Data Available";
		}
		if (!$l2)
		{
			$ISOS[$cc][2][$d] = array();
			//$ISOS[$cc][2][$d]["code"] = "N/A";
			//$ISOS[$cc][2][$d]["name"] = "No Level 2 Data Available";
		}
		fclose($fhandle);
	}
	if ($fhandle = fopen($dir_path."/centers/".$cc."_centers.properties", "r"))
	{
		$level = 0;
		while(($buffer = fgets($fhandle, 4096)) !== false)
		{
			$buffer = utf8_encode($buffer);
			if ($buffer{0} === "#")
				continue;
			$exploded = explode("=", $buffer."=");
			if (trim($exploded[0])==="") continue;
			if ($level == 0)
			{
				$ISOS[$cc]['c'] = array();
				$ISOS[$cc]['c'][0] = array();
				$temp = trim($exploded[1]);
				$LL = explode(",", $temp.",");
				$ISOS[$cc]['c'][0]["lat"] = trim($LL[0]);
				$ISOS[$cc]['c'][0]["long"] = trim($LL[1]);
				$level = 1 ;
			}//THIS ONLY PARSES OUT NATIONAL CENTER LAT_LONGS FOR NOW
		}
	}
}
asort($ISOS);
//echo var_dump($ISOS);
echo json_encode($ISOS);
?>
