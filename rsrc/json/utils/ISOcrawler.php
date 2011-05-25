<?php
//THIS FILE OPENS UP THE STEM/DATA/GEOGRAPHY FOLDER
//AND COLLECTS NATIONAL 0,1,2 LEVEL GEOGRAPHY INFORMATION INCLUDE
//PLACE AND REGION NAMES AS WELL AS LAT AND LONG FOR GEOGRAPHIC CENTER OF THE COUNTRY
$ISOS = array();
$file_list = array();
$dir_path = "org/eclipse/stem/data/geography";

//OPEN THE DIRECTORY GET A LIST OF THE FILES
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

FOR EACH FILE OPEN IT AND BEGIN PARSING THE DATA
foreach($file_list as $key =>$file)
{
	if ($fhandle = fopen($dir_path."/".$file, "r"))
	{
		$l0 = false;
		while(($buffer = fgets($fhandle, 4096)) !== false)
		{
			$buffer = utf8_encode($buffer); //THESE FILES ARE NOT PROPERLY ENCODED WITH UTF-8 SO DO SO
			if($buffer{0} === "#")
				continue;
			$exploded = explode("=", $buffer."=");
			if (($lftside = trim($exploded[0])) ==="") continue;
			$level = substr_count($lftside, "-");//SOMEBODY MADE UP THEIR OWN ISO-CODES THAT DO NOT CONFORM TO ANY REGULAR STANDARD THE PEST WE CAN DO IS COUNT THE NUMBER OF HYPHENS IN THE DESCRIPTOR TO DETERMINE THE LEVEL
			if ($level == 0) 
			{
				if ($l0) continue;//THIS LINE IS NECESSARY B/C THE MKD (MACEDONIA) FILE IS COMPLETELY WRONG AND WILL CONTINUALLY OVERWRITE ITSELF BECAUSE IT DOES NOT CONFORM TO ANY STANDARD
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
				continue; //WE ARE INDEXING THIS BY ARRAY BY ISOCODE THEN LEVEL # AND IT WILL RETURN EITHER A NAME OR A LIST OF NAMES OF REGIONS AT THAT LEVEL 0, 1, 2, 3
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

//JUST FOR FUN LET'S GET THE NATIONAL GEOGRAPHIC CENTER OF EACH COUNTRY IT WILL BE [ISO]['C']	
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
echo json_encode($ISOS); //MAKE THIS A JSON
?>
