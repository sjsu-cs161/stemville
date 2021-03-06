<?php

//THIS FILE WILL CRAWL ALL THROUGH THE ECLIPSE/PLUGINS DIR TO FIND THE FILES THAT CONTAIN GRAPH AND MODEL DATA

$jarfile_list = array();
$graphfolderlookslike = "org.eclipse.stem.data";
$path_to_plugins_dir = "/home/user/langs/stem/plugins";//"/path/to/STEM/plugins/directory/on/this/machine/..";
$i = 0;
if ($dhandle = opendir($path_to_plugins_dir))
{
	while (false !== ($jarfile = readdir($dhandle)))
	{
		if ($jarfile == '.' || $jarfile == '..') continue;
		if (substr($jarfile,0,21) === $graphfolderlookslike)
		{	
			$jarfile_list[$i] = $jarfile;
			$i++;
		}		
	}

}
closedir($dhandle);
$graphs = array();
$models = array();
$g = 0;
$m = 0;
for ($j = 0; $j < $i; ++$j)
{

	$output = array();
	exec("jar -ft ".$path_to_plugins_dir."/".$jarfile_list[$j], $output);
	foreach ($output as $k=>$elem)
	{
		if (substr($elem,-6,6) === ".graph") //FIND ANY FILES THAT END IN .GRAPH OR .MODEL AND ADD THEM TO THE RESPECTIVE ARRAYS
		{
			$graphs[$g] = substr($jarfile_list[$j],0,-4)."/".$elem;
			$g++;
		}
		else if (substr($elem,-6,6) === ".model")
		{
			$models[$m] = substr($jarfile_list[$j],0,-4)."/".$elem;
			$m++;
		}
	}
}
if ($fhandle = fopen("ISO-3166-1.json","r"))  //OPEN UP THE ISO.JSON WE ARE GOING TO INDEX ALL OF THE GRAPH AND MODELS BY ISO 
{
	while(($buffer = fgets($fhandle, 8192)) !== false)
		$cc = $buffer;
	fclose($fhandle);
}
$cc = json_decode($cc, true);
//GRAPHS
/*
$graphs_json = array();
foreach ($cc as $key =>$elem)
{
	$graphs_json[$key] = array();
	$y = 0;
	for($x = 0; $x < $g; ++$x)
	{
		if (preg_match("/".$key."/", $graphs[$x]) == 1)
		{
			$graphs_json[$key][$y] = array(); //JUST SOME REGULAR EXPRESSION TYPE STUFF CASUAL USERS SHOULD NOT EDIT THIS CODE
			$graphs_json[$key][$y]["path"] = str_replace(".", "/", $graphs[$x]);
			$graphs_json[$key][$y]["path"] = str_replace("_1/1/1","", $graphs_json[$key][$y]["path"]);
			$graphs_json[$key][$y]["path"] = str_replace("/graph",".graph", $graphs_json[$key][$y]["path"]);
			preg_match('/\/[^\.\/]+\/[^\.\/]+\/[^\.\/]+.graph/', $graphs[$x], $f);
			$graphs_json[$key][$y]["file"] = substr($f[0], 1);
			$y++;
		}
	}// FOR EVERY GRAPH FILE FIGURE OUT WHICH COUNTRY IT BELONGS TO AND INDEX IT TO THE PROPER ISO CODE
}
echo json_encode($graphs_json);  //BUILD THIS JSON OF ISO INDEXED GRAPHS
*/
//MODELS

$models_json = array();
foreach ($cc as $key =>$elem)
{
	$models_json[$key] = array();
	$y = 0;
	for($x = 0; $x < $m; ++$x)
	{
		if (preg_match("/".$key."/", $models[$x]) == 1)
		{
			$models_json[$key][$y] = array(); //REGULAR EXPRESSION STUFF CASUAL USERS SHOULD NOT EDIT THIS CODE
			$models_json[$key][$y]["path"] = str_replace(".","/", $models[$x]);
			$models_json[$key][$y]["path"] = str_replace("_1/1/1","",$models_json[$key][$y]["path"]); 
			$models_json[$key][$y]["path"] = str_replace("/model",".model", $models_json[$key][$y]["path"]);
			$models_json[$key][$y]["path"] = str_replace(".models","/models",$models_json[$key][$y]["path"]);
			preg_match('/\/[^\.\/]+\/[^\.\/]+\/[^\.\/]+.model/', $models[$x],$f);
			$models_json[$key][$y]["file"] = substr($f[0],1);
			$y++;

		}  //SAME THING LOOK AT EACH MODEL THAT WE FOUND AND INDEX IT TO AN ISO CODE
	}
}
echo json_encode($models_json); //BUILD THIS JSON OF ISO CODE INDEXED MODELS

?>
