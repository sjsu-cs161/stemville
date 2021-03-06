<?php
// getdata.php is used to retrieve STEM output (csv) data based on given input parameters
require_once 'settings.php';


// Collect GET parameters sent by the frontend: project name, scenario, level, and type
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

// If not all parameters were given, halt execution
if (!isset($project) || !isset($scenario) || !isset($level) || !isset($type)) {
    echo json_encode(Array("status" => "error", "msg" => "invalid arguments"));
    die();
}

// Some parameters need to be converted into long format
if ($type == 'POP_COUNT') {
    $type = 'Population Count';
} else if ($type == 'INCIDENCE') {
    $type = 'Incidence';
} else if ($type == 'DISEASE_DEATHS') {
    $type = 'Disease Deaths';
}

// Determines path to workspace based on parameters
$path_to_workspace = STEM_ROOT_PATH."/workspace/" . $project . '/Recorded Simulations';
// Find the newest dir generated by STEM
$dir_list = scandir($path_to_workspace, -1);
$scenario_folder = $dir_list[0];

// TODO: fix the following line to allow a custom disease name
// Right now we have locked all disease names to "dis1"
$disease_folder = "dis1/human"; //FOR NOW...

// Because we have experienced some randomness in STEM out put
// double check the format of the output dir 
if (!is_dir($path_to_workspace . '/' . $scenario_folder . '/' . $disease_folder)) {
    $disease_folder = "human";
}

// Construct the absolute path to the output directory
$OUTPUT_DIR = STEM_ROOT_PATH.'/workspace/'.$project.'/Recorded Simulations/'.$scenario_folder.'/'.$disease_folder.'/';

// input path is dependent on the provided data type
$INPUT_PATH = $OUTPUT_DIR.$type.'_'.$level.'.csv';



// Before fetching data, make sure a start interval and an amount of data to be fetched have been provided
if (isset($_GET['start']) && isset($_GET['amount'])) {
    $start = intval($_GET['start']);
	$end = $start+intval($_GET['amount']);
	$iter = $start;
    
    // But if we cant repeatedly find it, go die after $MAX_ITER tries
    $MAX_ITER = 20;
    $CUR_ITER = 0;
    
    // Invalid input, halt
    if ($iter <= 0) {
        echo json_encode(Array("status" => "error", "msg" => "INVALID INPUT PARAMETER(S)"));
        die();
    }
    while($CUR_ITER++ < $MAX_ITER) {
        $handle = fopen($INPUT_PATH, "r");
        if ($handle) {

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
				die();
            }
        }
        
        fclose($handle);

        // Requested data is not in the file yet, sleep and check again!
        sleep(1); // sleep 1 second
    }
    echo json_encode(Array("status" => "error", "msg" => "NO DATA AVAILABLE"));
} else {
    echo json_encode(Array("status" => "error", "msg" => "INVALID INPUT PARAMETER(S)"));
}

?>
