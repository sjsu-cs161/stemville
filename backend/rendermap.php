<?php

require_once 'settings.php';

	global $INPUT_FILE;
	global $bool;
	global $maxS, $maxN, $maxW, $maxE;
	global $mapArray;
	global $ll_data;
	
	require_once('MongoClass.php');
	
	// TEMP / FALLBACK VARIABLES
    $MAP_SCALE_X = 590; //?  LETS FOLLOW DISPLAY CONV THAT X >= Y  so 640 x 480 590 x450 etc 
    $MAP_SCALE_Y = 450; // IS the cANVAS ALSO THIS SIZE? canvas =  raphael(i,j, i+590,j+450)?
	$COUNTRY = 'ITA';
	$LEVEL = 1;
	
	if (isset($_GET['project'])) {
	    $mc = new MongoClass();
	    $rec = $mc->getRecord($_GET['project']);
	    $map = $rec[maps][0];
        $COUNTRY = $map[country];
        $LEVEL = $map[level];
	}
    if (strlen($COUNTRY) != 3 || !isset($LEVEL)) {
        echo json_encode(Array("status" => "error", "msg" => "Query did not match any results"));
        die();
    }
	if (isset($_GET['scale_x'])) {
	    $MAP_SCALE_X = intval($_GET['scale_x']);
	}
	if (isset($_GET['scale_y'])) {
	    $MAP_SCALE_Y = intval($_GET['scale_y']);
	}
	
	$COUNTRY = strtoupper($COUNTRY);
	$INPUT_FILE = "../rsrc/svg/".$COUNTRY."/".$COUNTRY."_".$LEVEL."_MAP.xml";

//****************************************************************************************
    /*
     * Scales an input lattitude or longitude value based on the largest and smallest lattitude and
     * longitude values of the GML map data. Uses spread values to give proper scaling
     * of countries like Russia, which has a huge east to west spread.
     * @param $degreesE minimum east longitude
     * @param $degreesW maximum west longitude
     * @param $degreesPX input longitude value
     * @param $degreesN maximum north lattitude
     * @param $degreesS minimum south lattitude
     * @param $degreesPY input lattitude value
     * @return returns an array with the scaled x and y values
     */
	function scale($degreesE, $degreesW, $degreesPX, $degreesN, $degreesS, $degreesPY)
	{
		$ySpread = $degreesN - $degreesS;
		$xSpread = $degreesE - $degreesW;
		$xy = array('x'=>0,'y'=>0);

		if (abs($ySpread) >= abs($xSpread))
		{
			$diff = abs($ySpread) - abs($xSpread);
			$diff /= 2.0;
			$degreesW -= $diff;			
			$xy['y'] = (($degreesN - $degreesPY)*(1/$ySpread));
			$xy['x'] = (($degreesPX - $degreesW)*(1/$ySpread));
		}else
		{
			$diff = abs($xSpread) - abs($ySpread);
			$diff /= 2.0;
			$degreesN += $diff;
			$xy['y'] = (($degreesN - $degreesPY)*(1/$xSpread));
			$xy['x'] = (($degreesPX - $degreesW)*(1/$xSpread));
		}
		return $xy;
	}
//******************************************************************************
    /*
     * Converts the lattitude and longitude values from the GML data to x and y
     * coordinates. Shifts x values based on y to x scale ratio.
     * @param $data array of lattitude and longitude values from the GML data
     * @return returns string of scaled x and y values
     */
 	function latLonToXY($data)
	{
        global $maxN;
        global $maxS;
        global $maxE;
        global $maxW;
		global $dWE;
		global $dNS;
        global $MAP_SCALE_X;
        global $MAP_SCALE_Y;
            	
		$ratio = $MAP_SCALE_Y/$MAP_SCALE_X;
		$shift = ($MAP_SCALE_X - $MAP_SCALE_X*$ratio)/2.0;
		
		$latLonToXY = "";
        $latLonArray = $data;

        $y = "";
		$limit = sizeof($latLonArray);
		$limit--;

		for ($i = 0; $i < $limit; $i = $i + 2)
		{
			$xy = array();
			$xy = scale($maxE, $maxW, $latLonArray[$i+1],$maxN, $maxS, $latLonArray[$i]);
			$x = $shift + $xy['x']*$MAP_SCALE_X*$ratio;
			$y = $xy['y']*$MAP_SCALE_Y;
			$latLonToXY .= $x." ";
			$latLonToXY .= $y." ";
		}
		return $latLonToXY;
    }
//******************************************************************************
    /*
     * Converts scaled x and y coordinates into SVG path data. Data is
     * stored in an array whose name is a region's ISO 3166 code and whose
     * value is an array of paths that make up that region.
     */
    function encodeSVGpath()
	{	
		global $mapArray;	        
		global $ll_data;
		global $nll;
        global $isoRC;
        global $isoRCiter;
        $posListCt = 0;

		for ($i = 0; $i < $nll; ++$i)
		{
			$posLis = $ll_data->item($i);
			$DAT = explode(" ",$posLis->nodeValue);
			$DAT = latLonToXY($DAT);
			$DAT = "M".trim($DAT)."Z";
            $posListLen = $isoRC->item($isoRCiter)->getElementsByTagName('posList')->length;
            if ($posListCt >= $posListLen){
                $isoRCiter++;
                $posListCt = 1;
            } else $posListCt++;
            $regionCode = $isoRC->item($isoRCiter)->attributes->item(0)->value;
			$mapArray[$regionCode][] = $DAT;
		}
     }
//******************************************************************************	
	// Parsing of the GML xml file and setting of the maximum and minimum lat/lon
    // values is done here
	$bool = true;
	$mapArray = array();
			
	$xdoc = new DomDocument;
	$xdoc->Load($INPUT_FILE);
	$isoCC = $xdoc->getElementsByTagName('title');
    $isoRC = $xdoc->getElementsByTagName('Polygon');
	$ll_data = $xdoc->getElementsByTagName('posList');
	$nll = $ll_data->length;
	$niso = $isoCC->length;
    $isoRCiter = 0;
    //echo "ISO Code: ".$isoCC->."<br />";
	//echo "isoCC data size: ".$niso." ll_data size: ".$nll."<br/>";
	for ($i = 0; $i < $niso; ++$i)
	{
		$isoCC = $isoCC->item($i);
	}
	
	for ($i = 0; $i < $nll; ++$i)
	{
		$posLis = $ll_data->item($i);
		//echo "".$posLis->tagName."</br>";
		//echo "".$posLis->nodeValue."<br/>";
		$DAT = explode(" ",$posLis->nodeValue);
		if ($bool)
		{
			$maxN = $DAT[0];
			$maxS = $maxN;
			$maxE = $DAT[1];
			$maxW = $maxE;
			$bool = false;
		}
		for($j = 0; $j < count($DAT); ++$j)
		{
			if (is_numeric($DAT[$j]))
			{			
				if (fmod($j, 2) == 0)
				{
					if ($DAT[$j] > $maxN) $maxN = $DAT[$j];
					else if ($DAT[$j] < $maxS) $maxS = $DAT[$j];
				}
				else
				{
					if ($DAT[$j] > $maxE) $maxE = $DAT[$j];
					else if ($DAT[$j] < $maxW) $maxW = $DAT[$j];
				}
			}		
		}

	}
	//echo "Baseline Data (NSEW): ".$maxN.", ".$maxS.", ".$maxW.", ".$maxE."<br />";
	//echo "".$isoCC->nodeValue."<br/>";
	encodeSVGpath();
    
    // Render map
    echo json_encode(Array("status" => "success", "data" => $mapArray));
?>
