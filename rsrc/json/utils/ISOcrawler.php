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

foreach($file_list as $key =>$file)
{
	if ($fhandle = fopen($dir_path."/".$file, "r"))
	{
		$l0 = false;
		while(($buffer = fgets($fhandle, 4096)) !== false)
		{
			$buffer = utf8_encode($buffer); //these are not encoded properly thanks IBM
			if($buffer{0} === "#")
				continue;
			$exploded = explode("=", $buffer."=");
			if (($lftside = trim($exploded[0])) ==="") continue;
			$level = substr_count($lftside, "-");//you also made up ISO-codes that don't conform to length standards so I hope this hypen counting hack works...thanks
			if ($level == 0) 
			{
				if ($l0) continue;//necessary b/c MKD file is completely wrong and everything gets overwritten as level 0 thanks ibm
				$l0 = true;
				$cc = $lftside;
				$ISOS[$cc] = array();
				$ISOS[$cc][0] = trim($exploded[1]);
				$ISOS[$cc][1] = array();
				$ISOS[$cc][2] = array();
				$ISOS[$cc][3] = array();
				$ISOS[$cc]['c'] = ""; 
				$level = 1;
				$c = 0;
				$d = 0;
				$e = 0;
				continue;
			}
			if ($level == 1)
			{
				$ISOS[$cc][$level][$c] = array();
				$ISOS[$cc][$level][$c]["code"] = $lftside;
				$ISOS[$cc][$level][$c++]["name"] = trim($exploded[1]);
				continue;
			}
			if ($level == 2)
			{
				$ISOS[$cc][$level][$d] = array();
				$ISOS[$cc][$level][$d]["code"] = $lftside;
				$ISOS[$cc][$level][$d++]["name"] = trim($exploded[1]);
				continue;
			}
			if ($level == 3)
			{
				$ISOS[$cc][$level][$e] = array();
				$ISOS[$cc][$level][$e] ["code"] = $lftside;
				$ISOS[$cc][$level][$e++]["name"] = trim($exploded[1]);
				
			}

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
