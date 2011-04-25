<?php

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
		if (substr($elem,-6,6) === ".graph")
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
if ($fhandle = fopen("ISO-3166-1.json","r"))
{
	while(($buffer = fgets($fhandle, 8192)) !== false)
		$cc = $buffer;
	fclose($fhandle);
}
$cc = json_decode($cc, true);
//GRAPHS
$graphs_json = array();
foreach ($cc as $key =>$elem)
{
	$graphs_json[$key] = array();
	$y = 0;
	for($x = 0; $x < $g; ++$x)
	{
		if (preg_match("/".$key."/", $graphs[$x]) == 1)
		{
			$graphs_json[$key][$y] = array(); 
			$graphs_json[$key][$y]["path"] = $graphs[$x];
			preg_match('/\/[^\.\/]+\/[^\.\/]+\/[^\.\/]+.graph/', $graphs[$x], $f);
			$graphs_json[$key][$y]["file"] = substr($f[0], 1);
			$y++;
		}
	}
}
echo json_encode($graphs_json);

//MODELS
/*
$models_json = array();
foreach ($cc as $key =>$elem)
{
	$models_json[$key] = array();
	$y = 0;
	for($x = 0; $x < $m; ++$x)
	{
		if (preg_match("/".$key."/", $models[$x]) == 1)
		{
			$models_json[$key][$y] = array();
			$models_json[$key][$y]["path"] = $models[$x];
			preg_match('/\/[^\.\/]+\/[^\.\/]+\/[^\.\/]+.model/', $models[$x],$f);
			$models_json[$key][$y]["file"] = substr($f[0],1);
			$y++;

		}
	}
}
echo json_encode($models_json);
*/
?>
