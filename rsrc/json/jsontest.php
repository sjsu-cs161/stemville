<?php

	$DOTJSONFILE = "selectoptions.json";
	
	if($fhandle = fopen($DOTJSONFILE, "r"))
	{
		while (($buffer = fgets($fhandle, 4096)) !== false)
		{
			$DOTJSONFILE = $buffer;
			echo var_dump(json_decode($DOTJSONFILE)); 
		}
	}
	fclose($fhandle);

?>
