<?php


function create_scen ($dir) {
	$data = $_POST['data'];
	#create the scenario object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.core.scenario:Scenario");
	$dublinCore = $doc->createElement("dublinCore");
	$model = $doc->createElement("model");
	$sequencer = $doc->createElement("sequencer");
	$scenarioDecorators = $doc->createElement("scenarioDecorators");
	$solver = $doc->createElement("solver");
	$dublin2 = $doc->createElement("dublinCore");
	$v = $data[scenario][name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.scenario","http:///org/eclipse/stem/core/scenario.ecore");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.solvers.fd","http:///org/eclipse/stem/solvers/fd");
	$eclipse->setAttribute("uRI","platform:/resource/$dir/scenarios/$v.scenario");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Scenario");
	$value = $data[scenario][solver];
	$n = $data[scenario][name];
	$n_s = split($n, ".scenario");
	$dublinCore->setAttribute("identifier", "platform:/resource/$data[project_name]/scenarios/$n");
	$dublinCore->setAttribute("description","Scenario &quot;$n_s&quot;");
	$dublinCore->setAttribute("format","http:///org/eclipse/stem/core/scenario.ecore");
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/Scenario");
	
	$list = array_keys($data[scenario]);
	foreach ($list as $i) {
		if ($i == "model") {
			$model->setAttribute("href", "platform:/resource/$dir/models/" . $data[scenario][model] . "#/");
		}
		if ($i == "sequencer") {
			$sequencer->setAttribute("xsi:type", "org.eclipse.stem.core.sequencer:SequentialSequencer");
			$sequencer->setAttribute("href", "platform:/resource/$dir/sequencers/" . $data[scenario][sequencer] . "#/");
		}
		if ($i == "solver") {
			$value = $data[scenario][solver];
			#dublin core for scenario object... actually i found out this is optional...
			#solver for scenario object
			if($value == "FiniteDifferenceImpl") {
				$solver->setAttribute("xsi:type", "org.eclipse.stem.solvers.fd:FiniteDifference");
				$solver->setAttribute("uRI", "stem://org.eclipse.stem/FiniteDifferenceSolver/59D0CC01154C55B8");
				$solver->setAttribute("typeURI", "stemtype://org.eclipse.stem/Solver");

				$dublin2->setAttribute("identifier", "stem://org.eclipse.stem/FiniteDifferenceSolver/59D0CC01154C55B8");
				$dublin2->setAttribute( "type", "stemtype://org.eclipse.stem/Solver");
			} else {
			#scince there are only 2 options the answer should be RungeKuttaImpl
				$solver->setAttribute("xsi:type", "org.eclipse.stem.solvers.rk:RungeKutta");
				$solver->setAttribute("uRI", "stem://org.eclipse.stem/RungeKuttaSolver/46316E0140EE9BA2");
				$solver->setAttribute("typeURI", "stemtype://org.eclipse.stem/Solver");
	
				$dublin2->setAttribute("identifier", "stem://org.eclipse.stem/RungeKuttaSolver/46316E0140EE9BA2");
				$dublin2->setAttribute( "type", "stemtype://org.eclipse.stem/Solver");
			}
		}
		if ($i == "infector") {
			$scenarioDecorators->setAttribute("href","platform:/resource/$dir/decorators/" . $data[scenario][infector] . "#/");
		}
		if ($i == "inoculators") {
			#there can be multiple innoculators in a scenario... we need to make this a list.
			# in other words the gui needs to be changed.
			$arr = $data[scenario][inoculators];
			foreach ($arr as $k) {
				$scenarioDecorators->setAttribute("href", "platform:/resource/UsaMexico/decorators/" . $i  . "#/");
			}
		}
		if ($i == "trigger") {
		#fix this later... not so important...	
		}
	}
	#save up the scenario object.
	$eclipse->appendChild($dublinCore);
	$eclipse->appendChild($model);
	$eclipse->appendChild($sequencer);
	$eclipse->appendChild($scenarioDecorators);
	$solver->appendChild($dublin2);
	$eclipse->appendChild($solver);
	$doc->appendChild($eclipse);
	$doc->save($dir . "/scenarios/$v.scenario");


}


function create_sequencer ($dir) {
	$data = $_POST['data'];
	#create the scenario object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.core.sequencer");
	$dublinCore = $doc->createElement("dublinCore");	
	$startTime = $doc->createElement("startTime");
	$endTime = $doc->createElement("endTime");
	$currentTime = $doc->createElement("currentTime");
	$v = $data[sequencer][name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$dir/sequencer/$v.sequencer");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");




	$time = $data[sequencer][cycle_period];
	$values = explode(" ", $time);
echo $time . "\n";
	if ( $values[1] == "days" ) {
		$t = $value[0]*24*60*60*1000;
		$eclipse->setAttribute("duration", $t);
echo $t ."\n";
	}
	
	$arr = $data[sequencer];
	$startTime->setAttribute("startTime", $data[sequencer][start_date] . 								"T12:00:00.265-0700");
	$d;
	if ($values[1] == "days") {
		$d = date('Y-m-d', strtotime($data[sequencer][start_data]. " +$values[0]days"));
		$endTime->setAttribute("endTime", "$d");	
	}
	$currentTime->setAttribute("currentTime", $data[sequencer][start_date] .
         			    "T12:00:00.265-0700");
 

	$dublinCore->setAttribute("titile", "");
	$dublinCore->setAttribute("identifier","platform:/resource/$data[project_name]/sequencers/$v.sequencer");
	$dublinCore->setAttribute("description","");
	$dublinCore->setAttribute("creator","");
	$dublinCore->setAttribute("format","http://org.eclipse.stem/Identifiable");
	$dublinCore->setAttribute("source","");
	$dublinCore->setAttribute("type","stemtype://org.ecplise.stem/Identifiable");
	$dublinCore->setAttribute("created","");
	$dublinCore->setAttribute("valid","start=$data[sequencer][start_time]; end=$d");
	
	$eclipse->appendChild($dublinCore);	
	$eclipse->appendChild($startTime);	
	$eclipse->appendChild($endTime);	
	$eclipse->appendChild($currentTime);	
	$doc->appendchild($eclipse);	
	$doc->save($dir . "/sequencers/$v.sequencer");

}


function create ($dir) {
$data = $_POST['data'];
#create the sequencer object... there are 2 types of sequencers, for now we will deal with 1 (sequential).
$doc = new DOMDocument();
$doc->formatOutput = true;
$eclipse = $doc->createElement("org.eclipse.stem.core.sequencer:SequentialSequencer");
$dublinCore = $doc->createElement("dublinCore");
$time = $doc->createElement("startTime");
$v = $data[sequencer][name];

$eclipse->setAttribute("xmi:version","2.0");
$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
$eclipse->setAttribute("uRI","platform:/resource/$dir/sequencers/$v.sequencer");
$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");
$eclipse->setAttribute("timeIncrement", $data[sequencer][cycle_period]);

$dublinCore->setAttribute("identifier", "platform:/resource/$dir/sequencers/" . $data[sequencer][name] . ".sequencer");
$dublinCore->setAttribute("description", "Sequential Sequencer &quote;" . $data[sequencer][name]
							. "&quote; starting from " . $data[sequencer][start_date]
							. ", with no end and a cycle period of " . $data[sequencer][cycle_period] . ".");
$dublinCore->setAttribute("creator", "");
$dublinCore->setAttribute("format", "http:///org/eclipse/stem/core/sequencer.ecore");
$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/Sequencer");
$dublinCore->setAttribute("created", "");
$dublinCore->setAttribute("valid", "start=" . $data[sequencer][start_date] . ";");

$time->setAttribute("time", $data[sequencer][start_date] . "T12:00:00.691-0700");

$eclipse->appendChild($dublinCore);
$eclipse->appendChild($time);
$doc->appendChild($eclipse);
$doc->save($dir . "/sequencers/$v.sequencer");



#making infector object
$doc = new DOMDocument();
$doc->formatOutput = true;
$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:SIInfector");
$dublinCore = $doc->createElement("dublinCore");
$v = $data[infector][name];

$eclipse->setAttribute("xmi:version","2.0");
$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
$eclipse->setAttribute("xmlns:org.eclipse.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
$eclipse->setAttribute("uRI","platform:/resource/$dir/decorators/$v.standard");
$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");
$eclipse->setAttribute("diseaseName", $data[disease][name]);
$eclipse->setAttribute("targetISOKey", $data[infector][location]);
$eclipse->setAttribute("populationidentifier", $data[infector][population]);
$eclipse->setAttribute("infectiousCount", $data[infector][abs_or_percent]);
$dublinCore->setAttribute("identifier", "platform:/resource/$dir/decorators/$data[infector][name].standard");
$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore");

$eclipse->appendChild($dublinCore);
$doc->appendChild($eclipse);
$doc->save($dir . "/decorators/$v.standard");



#making disease object
$doc = new DOMDocument();
$doc->formatOutput = true;
$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:DeterministicSEIRDiseaseModel");
$dublinCore = $doc->createElement("dublinCore");
$v = $data[disease][name];
$eclipse->setAttribute("xmi:version","2.0");
$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
$eclipse->setAttribute("xmlns:org.eclipse.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
$eclipse->setAttribute("uRI","platform:/resource/$dir/decorators/$v.standard");
$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");
$eclipse->setAttribute("diseaseName", $data[disease][name]);
$eclipse->setAttribute("recoveryRate", $data[disease][infections_recovery_rate]);
$eclipse->setAttribute("incubationRate",$data[disease][incubation_rate]);
$eclipse->setAttribute("immunityLossRate", $data[disease][immunity_loss_rate]);
$eclipse->setAttribute("transmissionRate", $data[disease][transmission_rate]);
$eclipse->setAttribute("incubationRate", $data[disease][incubation_rate]);

$dublinCore->setAttribute("identifier", "platform:/resource/$dir/decorators/$data[disease][name].standard");
$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore" );
$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/diseasemodel" );

$eclipse->appendChild($dublinCore);
$doc->appendChild($eclipse);
$doc->save($dir . "/decorators/$v.standard");

}

function create_models($dir) {
	$data = $_POST['data'];
	#create the scenario object
	create_model($dir, $data[models]);
	

}

function create_model($dir, $mod) {
	$data = $mod;
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.core.model:Model");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[name];
echo "$v\n";
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.model","http:///org/eclipse/stem/core/model.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$dir/models/$v.model");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Model");
	
	$dublinCore->setAttribute("identifier","platform:/resource/$dir/models/$v.model");
	$dublinCore->setAttribute("creator","");
	$dublinCore->setAttribute("date","");
	$dublinCore->setAttribute("format","http:");
	$dublinCore->setAttribute("type","");
	$dublinCore->setAttribute("created","");
	$arr = $data[models];
	foreach ($data as $m) {
echo "$m\n";
	}

}


$data = $_POST['data'];
//$nfile = fopen("p.txt", "w");
//fwrite($nfile, $data[scenario][name]);
//fwrite($nfile, "test\n");
$p_name = "/var/www/cs161/group2/stem/workspace/" . $data[project_name];
mkdir($p_name, 0777);
mkdir($p_name . "/decorators" , 0777);
mkdir($p_name . "/experiments" , 0777);
mkdir($p_name . "/graphs", 0777);
mkdir($p_name . "/models", 0777);
mkdir($p_name . "/modifiers", 0777);
mkdir($p_name . "/predicates", 0777);
mkdir($p_name . "/scenarios", 0777);
mkdir($p_name . "/sequencers", 0777);
mkdir($p_name . "/RecordedSimulations", 0777);

create_scen($p_name);
create_sequencer($p_name);
create_models($p_name);
#create("$p_name")
?>
