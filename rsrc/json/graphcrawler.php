<?php

$jarfile_list = array();
$graphfolderlookslike = "org.eclipse.stem.data";
$path_to_plugins_dir = "/path/to/STEM/plugins/directory/on/this/machine/..";
$i = 0;
//chdir("../");
if ($dhandle = opendir($path_to_plugins_dir))
{
	while (false !== ($jarfile = readdir($dhandle)))
	{
		if ($jarfile == '.' || $jarfile == '..') continue;
		if (substr($jarfile,0,21) === $graphfolderlookslike)
		//if (substr($jarfile, -4,4) === ".jar")
		{	
			$jarfile_list[$i] = $jarfile;
			$i++;
		}		
	}

}
closedir($dhandle);
//echo json_encode($jarfile_list);
$graphs = array();
$models = array();
$g = 0;
$m = 0;
for ($j = 0; $j < $i; ++$j)
{

	$output = array();
	//echo "\n";
	exec("jar -ft ".$path_to_plugins_dir."/".$jarfile_list[$j], $output);
	//echo json_encode($output);
	//echo "\n";
	foreach ($output as $k=>$elem)
	{
		if (substr($elem,-6,6) === ".graph")
		{
			$graphs[$g] = $jarfile_list[$j]."/".$elem;
			$g++;
		}
		else if (substr($elem,-6,6) === ".model")
		{
			$models[$m] = $jarfile_list[$j]."/".$elem;
			$m++;
		}
	}
}
echo var_dump($graphs);
echo var_dump($models);
?>
