<?php


if (isset($_GET['project'])) {
    $project = $_GET['project'];
}
if (isset($_GET['scenario'])) {
    $scenario = $_GET['scenario'];
}
if (isset($_GET['level'])) {
    $level = $_GET['level'];
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

if (!isset($project) || !isset($scenario) || !isset($level) || !isset($type)) {
    echo json_encode(Array("status" => "error", "msg" => "invalid arguments"));
    die();
}

if ($type == 'POP_COUNT') {
    $type = 'Population Count';
} else if ($type == 'INCIDENCE') {
    $type = 'Incidence';
} else if ($type == 'DISEASE_DEATHS') {
    $type = 'Disease Deaths';
}


// TODO: PLEASE FIGURE OUT THE REST OF THE PATH (VARIES W/ DATE)
// DON'T FORGET TO APPEND TRAILING SLASH (/) TO $OUTPUT_DIR
$OUTPUT_DIR = '/home/tseng/group2/stem/workspace/'.$project.'/Recorded Simulations/';



$INPUT_PATH = $OUTPUT_DIR.$type.'_'.$level.'.csv';
// ##########################
// ##### END SETTINGS #######
// ##########################


if (isset($_GET['start']) && isset($_GET['amount'])) {
    $start = intval($_GET['start']);
	$end = $start+intval($_GET['amount']);
	$iter = $start;
    
    // We have yet to find what is requested
    $RESULT_FOUND = false;
    // But if we cant repeatedly find it, go die after $MAX_ITER tries
    $MAX_ITER = 30;
    $CUR_ITER = 0;
    // Lets look for patterns. If the size of the file hasn't changed since last try, then data is most likely not being logged
    $RESULT_SIZE = 0;
    
    if ($iter <= 0) {
        echo json_encode(Array("error" => "INVALID INPUT PARAMETER"));
        $RESULT_FOUND = true;
    }

        $handle = fopen($INPUT_PATH, "r");
        if ($handle) {
            // Lets read the whole file. This could be bad if we are dealing with humoungously large files
            if (filesize($INPUT_PATH) > $RESULT_SIZE) {
                $RESULT_SIZE = filesize($INPUT_PATH);
            } else if (filesize($INPUT_PATH) == $RESULT_SIZE) {
                echo json_encode(Array("error" => "FILE NOT UPDATING"));
                $RESULT_FOUND = true; // Not really though
            }
            $contents = fread($handle, filesize($INPUT_PATH));
            
            // Split the document into an array where each row in the file represents a row in the array
            // ie, $rows[11] refers to iteration 11, etc.
            // The first row ($rows[0]) contains the column fields and should be used in construction JSON data
            $rows = preg_split('/\n/', $contents);    

            // Get the requested iteration

            // TODO: Check the CVS output files, STEM seems to add a linebreak after the last line
            // so actual rows = recorded rows - 1
            if ($start > 0 && $start < sizeof($rows)-1) {
                // Perhaps the ugliest hack of all time
                // This creates an associative array (title1 => data1, title2 => data2, etc) that we can nicely encode to JSON
                // Title is meant to be whatever value is in the first row of the CSV output
                $title = preg_split('/,/', $rows[0]);
				$out = Array("status" => "success", "data" => Array());
				
				for ($iter = $start; $iter <= $end && $iter < sizeof($rows)-1; $iter++) {
                	$data = preg_split('/,/', $rows[$iter]);
	                $len = sizeof($title);
	                $output = array();
	                for ($i=0;$i<$len;$i++) {
	                    // We definitely want our NUMERIC data represented as FLOAT (because sometimes its integer, sometimes decimal)
	                    $output[$title[$i]] = is_numeric($data[$i]) ? floatval($data[$i]) : $data[$i];
	                }
					//$out["data"][$iter] = $output;	                
					array_push($out["data"], $output);
				}
				
				echo json_encode($out);
				//echo json_encode($output);
                $RESULT_FOUND=true;
            } else {
                // Requested data is not in the file yet, sleep and check again!
                echo json_encode(Array("status" => "success", "msg" => "NO MORE DATA", "data" => Array()));
            }

        } else {
            echo json_encode(Array("status" => "error", "msg" => "COULD NOT READ DATA FROM FILE"));
            $RESULT_FOUND = true; // Nope, but we don't want the computer to explode
        }
        fclose($handle);

} else if (isset($_GET['params'])) {
    $handle = fopen($INPUT_PATH, "r");
    if ($handle) {
        $contents = fread($handle, filesize($INPUT_PATH));
        $rows = preg_split('/\n/', $contents);    
        $data = preg_split('/,/', $rows[0]);
        $output = Array('params' => $data);
        echo json_encode($output);
    } else {
        echo json_encode(Array("status" => "error", "msg" => "COULD NOT READ FILE"));
    }
    fclose($handle);
} else {
    echo json_encode(Array("status" => "error", "msg" => "INVALID INPUT PARAMETER(S)"));
}

?>