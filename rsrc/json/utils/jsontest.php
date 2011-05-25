<?php
//THIS FILE DOES NOTHING MORE THAN PERFORM A QUICK JSON TEST FOR WELL FORMEDNESS.  SIMPLY PUT THE NAME OF THE FILE YOU WANT TO TEST INTO THE VARIABLE $DOTSJONFILE
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
