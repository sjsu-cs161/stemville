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
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.scenario","http:///org/eclipse/stem/core/scenario.ecore");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.solvers.fd","http:///org/eclipse/stem/solvers/fd");
	$eclipse->setAttribute("uRI","platform:/resource/$n/scenarios/$v.scenario");
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
			$model->setAttribute("href", "platform:/resource/$n/models/" . $data[scenario][model] . "#/");
		}
		if ($i == "sequencer") {
			$sequencer->setAttribute("xsi:type", "org.eclipse.stem.core.sequencer:SequentialSequencer");
			$sequencer->setAttribute("href", "platform:/resource/$n/sequencers/" . $data[scenario][sequencer] . "#/");
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
			$scenarioDecorators->setAttribute("href","platform:/resource/$n/decorators/" . $data[scenario][infector] . "#/");
		}
		if ($i == "inoculators") {
			#there can be multiple innoculators in a scenario... we need to make this a list.
			# in other words the gui needs to be changed.
			$arr = $data[scenario][inoculators];
			foreach ($arr as $k) {
				$scenarioDecorators->setAttribute("href", "platform:/resource/$n/decorators/" . $i  . "#/");
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
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/sequencer/$v.sequencer");
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


function create_models($dir) {
	$data = $_POST['data'];
	#create the scenario object
	create_model($dir, $data[models], $data[project_name], $data[disease], $data[infector]);
	

}

function create_model($dir, $mod, $pname, $dis, $inf) {
	$data = $mod;
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.core.model:Model");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.model","http:///org/eclipse/stem/core/model.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$pname/models/$v.model");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Model");
	
	$dublinCore->setAttribute("identifier","platform:/resource/$pname/models/$v.model");
	$dublinCore->setAttribute("creator","");
	$dublinCore->setAttribute("date","");
	$dublinCore->setAttribute("format","http:///org/eclipse/core/model.ecore");
	$dublinCore->setAttribute("type","stemtype://org.eclipse.stem/Model");
	$dublinCore->setAttribute("created","");
	$eclipse->appendChild($dublinCore);
	$arr = $data[models];
	foreach ($arr as $m) {
		$model = $doc->createElement("models");
		$model->setAttribute("href","platform:/resource/$pname/models/$m[name].model#/");
		$eclipse->appendChild($model);
	}
	$arr = array_unique($data[graphs]);
	foreach ($arr as $g) {
		$graph = $doc->createElement("graphs");
		$temp = explode("_1.1.1", $g);
		$gr = $temp[0] . $temp[1];
		$graph->setAttribute("href","platform:/$gr#/");
		$eclipse->appendChild($graph);
	}
	
	if($inf[infector_model] == $mod[name]) {
		$node = $doc->createElement("nodeDecorators");
		$node->setAttribute("href", "platform:/resource/$pname/decorators/$inf[name].standard#/");
		$eclipse->appendChild($node);
	}
	if($dis[disease_model] == $mod[name]) {
		$node = $doc->createElement("nodeDecorators");
		$node->setAttribute("href", "platform:/resource/$pname/decorators/$dis[name].standard#/");
		$eclipse->appendChild($node);
	}
	
	$doc->appendChild($eclipse);
	$doc->save($dir . "/models/$v.model");
	#make all sub models
	$arr = $data[models];
	foreach ($arr as $m) {
		create_model($dir, $m, $pname, $dis, $inf);
	}


}

function create_disease ($dir) {
	$data = $_POST['data'];
	#create the scenario object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:DeterministicSEIRDKodel");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[disease][name];
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.ecplise.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/decorators/$v.standard");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable1129451");

	$eclipse->setAttribute("backgroundMortalityRate", $data[disease][infectious_mortality_rate]);
	$eclipse->setAttribute("diseaseName", $data[disease][name]);
	$eclipse->setAttribute("recoveryRate", $data[disease][infections_recovery_rate]);
	$eclipse->setAttribute("incubationRate",$data[disease][incubation_rate]);
	$eclipse->setAttribute("immunityLossRate", $data[disease][immunity_loss_rate]);
	$eclipse->setAttribute("transmissionRate", $data[disease][transmission_rate]);
	$eclipse->setAttribute("incubationRate", $data[disease][incubation_rate]);

	$dublinCore->setAttribute("identifier", "platform:/resource/$n/decorators/$data[disease][name].standard");
	$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore" );
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/diseasemodel" );
	

	$eclipse->appendChild($dublinCore);
	$doc->appendChild($eclipse);
	$doc->save($dir . "/decorators/$v.standard");
	
}
function create_infector ($dir) {
	$data = $_POST['data'];
	#create the scenario object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:SIInfector");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[infector][name];
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.ecplise.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/decorators/$v.standard");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable1129656");

	$eclipse->setAttribute("targetISOKey", $data[infector][location]);
	$eclipse->setAttribute("diseaseName", $data[disease][name]);
	$eclipse->setAttribute("populationIdentifier", $data[infector][population]);
	$eclipse->setAttribute("infectiousCount","15.0");

	$dublinCore->setAttribute("identifier", "platform:/resource/$n/decorators/$data[infector][name].standard");
	$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore" );
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/identifiable1129656" );
	

	$eclipse->appendChild($dublinCore);
	$doc->appendChild($eclipse);
	$doc->save($dir . "/decorators/$v.standard");
	
}

function create_graphs ($dir) {
echo "made it here\n";
	$data = $_POST['data'];
	$arr = array_unique($data[graphs]);
	foreach ($arr as $g) {
		$h = explode("geography/",$g);
		$path = "/var/www/cs161/group2/stem/plugins/" . $h[1];
echo $path . " = path\n";
		exec("cp $path $dir/graphs/"); 	
echo "cp $path $dir/graphs\n";
	}	
}


$data = $_POST['data'];
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
create_disease($p_name);
create_infector($p_name);
create_graphs($p_name);
#create("$p_name")
$stem_path = "./var/www/cs161/group2/stem/";
echo "$stem_path/STEM -headless -log -uri platform:/resource/$data[project_name]/scenarios/$data[scenario][name].scenario\n";;
exec("$stem_path\STEM -headless -log -uri platform:/resource/$data[project_name]/scenarios/$data[scenario][name].scenario");

countryAutoLvl();
//Wicked AutoMagick finds the XXX country and level
function countryAutoLvl()
{
	$data = $_POST['data'];
	$iso = $data[country];
	$arr = array_unique($data[graphs]);
	$highLvl = 0;
	foreach ($arr as $key => $elem)
	{
		if ($success = preg_match('/'.$iso.'_[0-9]/',$elem, $match) == 1)
		{	
        		//echo $match[0]."\n";
        		$tmp = explode("_",$match[0]);
        		$lvl = $tmp[1];
        		//echo $lvl."\n";
			if ($lvl > $highLvl) $highLvl = $lvl;  
		}	
	}
	for ($i = $highLvl; $i > -1; $i--)
		if (file_exists("../rsrc/svg/".$iso."/".$iso."_".$i."_MAP.xml"))
		{
			//$highLvl = $i;
			break;
		}else
			$highLvl = $i - 1;
	if ($highLvl > -1)
	{
		$map_array = array(array('country' => $iso, 'level' => $highLvl));
		//DB Record
		require_once('MongoClass.php');
		$mc = new MongoClass();
		$mc->createRecord($data[project_name],$data[scenario][name], $map_array);
	}
	else
	{
		//GENERATE SOME ERROR
	}
}
?>
