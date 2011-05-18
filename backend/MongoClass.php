<?php

require_once 'settings.php';

class MongoClass
{
	// SET XML MAIN DIRECTORY & CSV MAIN DIRECTORIES
	public $xml_main_dir = STEMVILLE_ROOT_PATH;
	public $csv_main_dir = STEMVILLE_ROOT_PATH;

	// Creates a record that holds project names, scenario names, and needed maps
	// Returns 1 if successful
	public function createRecord($project, $scenario, $maps) {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem1;

			$record = array('project' => $project,
						  'scenario' => $scenario,
						  'maps' => $maps );

			$result = $collection->insert($record);

			return $result;

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Lists all the created records.
	// Returns an array of all the records. Note: first index is always the MongoID
	public function listAllRecords() {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem1;

			$listarray = array();
			$i = 0;

			$cursor = $collection->find()->sort(array('date'=>-1));

			foreach ($cursor as $obj) {
				$listarray[$i] = $obj;
				$i++;
			}

			return $listarray;

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Retrieves the recorded for the inputted project name
	// Returns the record. Note: first index is always the MongoID
	public function getRecord($proj_name) {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem1;

			$obj = $collection->findOne(array('project' => $proj_name) );
			return $obj;

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	public function removeRecord($proj_name) {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem1;

			$collection->remove(array('project' => $proj_name) );

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}


	// Inserts JSON object into the database with a specified scenario
	// Returns 1 if successful
	public function insertJSON($jsonObj, $scen_key) {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem;

			$assoArray = json_decode($jsonObj);
			$assoArrayKeyAppended = array('scenario_key' => $scen_key, 'data' => $assoArray);
			$result = $collection->insert($assoArrayKeyAppended);

			return $result;

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Retrieves JSON object for a specified scenario
	// Returns a JSON object if successful
	public function retrieveJSON($scen_key) {
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem;

			$obj = $collection->findOne(array('scenario_key' => $scen_key) );

			$json = json_encode($obj['data']);
			return $json;

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Converts an XML file into a JSON object, then persists it into the database using a scenario name.
	// Note: $fileLocation = unique scenario folder + filename
	public function insertXML($file_location, $scen_key)
	{
		try {
			$connection = new Mongo("localhost");
			$collection = $connection->test->stem;

			$complete_path = $this->xml_main_dir . $file_location;

			$xmlFileData = file_get_contents($complete_path);
			$xmlData = new SimpleXMLElement($xmlFileData);
			$jsonData = json_encode($xmlData);

			$this->insertJSON($jsonData,$scen_key);

			$connection->close();
		}
		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}

	}

	// Inserts a CSV file directly into the database using a scenario name.
	// Note: $fileLocation = unique user folder + filename
	public function insertCSV($file_location, $scen_key)
	{
		try {
			$connection = new Mongo("localhost");
			$db = $connection->test;
			$grid = $db->getGridFS();

			$complete_path = $this->csv_main_dir . $file_location;
			$id = $grid->storeFile($complete_path, array("scenario_key" => $scen_key) );

			return $id;

			$connection->close();
		}

		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Retrieves a CSV file from the database.
	// Returns a CSV file and the number of bytes written
	public function retrieveCSV($scen_key)
	{
		try {
			$connection = new Mongo("localhost");
			$db = $connection->test;

			$grid = $db->getGridFS();

			$file = $grid->findOne(array("scenario_key" => $scen_key) );

			$filename = $file->getFilename();
			$num_bytes = $file->write($filename);
			return $num_bytes;

			$connection->close();
		}

		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

	// Deletes all information for a specified scenario name.
	public function deleteAll($scen_key)
	{
		try {
		$connection = new Mongo("localhost");
		$db = $connection->test;
		$collection = $db->stem;
		$grid = $db->getGridFS();

		$scenario = array("scenario_key" => $scen_key);

		$removeFile = $grid->remove($scenario);				// remove CSV files

		$collection->remove($scenario, true);				// remove JSON data

		$connection->close();
		}

		catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}

}
?>
