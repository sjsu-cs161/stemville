<?php
/**
* create_scenario.php
* @author Tom Turney
* 
**/
require_once 'settings.php';
/**
* Function create_scen will create the scenario object
* from $data.
* 
* @param dir The workspace directory.
**/
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
	$dublinCore->setAttribute("identifier", "platform:/resource/$data[project_name]/scenarios/$n.scenario");
	$dublinCore->setAttribute("description","Scenario &quot;$n_s&quot;");
	$dublinCore->setAttribute("format","http:///org/eclipse/stem/core/scenario.ecore");
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/Scenario");
	
	$list = array_keys($data[scenario]);
	foreach ($list as $i) {
		if ($i == "model") {
			$model->setAttribute("href", "platform:/resource/$data[project_name]/models/" . $data[scenario][model] . "#/");
		}
		if ($i == "sequencer") {
			$sequencer->setAttribute("xsi:type", "org.eclipse.stem.core.sequencer:SequentialSequencer");
			$sequencer->setAttribute("href", "platform:/resource/$data[project_name]/sequencers/" . $data[scenario][sequencer] . "#/");
		}
		if ($i == "solver") {
			$value = $data[scenario][solver];
			#dublin core for scenario object
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
			$scenarioDecorators->setAttribute("href","platform:/resource/$data[project_name]/decorators/" . $data[scenario][infector] . "#/");
		}
		if ($i == "inoculators") {
			$arr = $data[scenario][inoculators];
			foreach ($arr as $k) {
				$scenarioDecorators->setAttribute("href", "platform:/resource/$n/decorators/" . $i  . "#/");
			}
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
	$v = $data[scenario][name];
	$doc->save($dir . "/scenarios/$v.scenario");
}

/**
* Function create_sequencer will create the sequencer object
* from $data.
* 
* @param dir The workspace directory.
**/
function create_sequencer ($dir) {
	$data = $_POST['data'];
	#create the sequencer object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.core.sequencer:SequentialSequencer");
	$dublinCore = $doc->createElement("dublinCore");	
	$startTime = $doc->createElement("startTime");
	$endTime = $doc->createElement("endTime");
	$currentTime = $doc->createElement("currentTime");
	$v = $data[sequencer][name];
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.core.sequencer","http:///org/eclipse/stem/core/sequencer.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/sequencers/$v.sequencer");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");

	$time = $data[sequencer][cycle_period];
	$values = explode(" ", $time);
	if ( $values[1] == "days" ) {
		$t = ((int)$values[0]*24*60*60*1000);
		$eclipse->setAttribute("timeIncrement", $t);
	}
	
	$arr = $data[sequencer];
	$taco = $data[sequencer][start_date];
	$startTime->setAttribute("time", $taco . "T12:00:00.265-0700");
	$d;
	$dublinCore->setAttribute("identifier","platform:/resource/$data[project_name]/sequencers/$v.sequencer");
	$dublinCore->setAttribute("description","");
	$dublinCore->setAttribute("creator","");
	$dublinCore->setAttribute("format","http://org.eclipse.stem/sequencer.ecore");
	$dublinCore->setAttribute("type","stemtype://org.ecplise.stem/Sequencer");
	$dublinCore->setAttribute("created","");
	$tempers = $data[sequencer][start_time];
        $dublinCore->setAttribute("valid","start=$tempers; end=$d");
	
	$eclipse->appendChild($dublinCore);	
	$eclipse->appendChild($startTime);	
	if ($values[1] == "days" && strcmp($data[end_date], "") != 0) {
		$endTime->setAttribute("time", $data[sequencer][end_date] .
					"T12:00:00.265-0700");
		$eclipse->appendChild($endTime);
	}
	#saves the sequencer object.
	$doc->appendchild($eclipse);	
	$doc->save($dir . "/sequencers/$v.sequencer");

}

/**
* Function create_models will create the model objects. Will pass each
* model in a list to the create_model funtion.
* from $data.
* 
* @param dir The workspace directory.
**/
function create_models($dir) {
	$data = $_POST['data'];
	#create the model object
	create_model($dir, $data[models], $data[project_name], $data[disease], $data[infector]);
	

}

/**
* Function create_model will create the model object
* from $data.
* 
* @param dir The workspace directory.
**/
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
	$dublinCore->setAttribute("format","http:///org/eclipse/stem/core/model.ecore");
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
		$gr = $g;
		if (strpos($gr, "population")) {
			$list = explode("/", $gr);
			$p = "platform:/plugin/org.eclipse.stem.data.geography.population.human/resources/data/country/" . $list[10] . "/" . $list[11] . "#/";
			$graph->setAttribute("href", $p);
		} else if (strpos($gr, "data/relationship")) {
			$list = explode("/", $gr);
			$p = "platform:/plugin/org.eclipse.stem.data.geography/resources/data/relationship/$list[8]/$list[9]#/"; 
			$graph->setAttribute("href", $p);
		} else {
			$list = explode("/", $gr);
			$p = "platform:/plugin/org.eclipse.stem.data.geography/resources/data/country/$list[8]/$list[9]#/";
			$graph->setAttribute("href", $p);
		}
		$eclipse->appendChild($graph);
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

/**
* Function create_disease will create the disease object
* from $data.
* 
* @param dir The workspace directory.
**/
function create_disease ($dir) {
	$data = $_POST['data'];
	#create the disease object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:DeterministicSEIRDiseaseModel");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[disease][name];
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/decorators/$v.standard");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");

	$eclipse->setAttribute("backgroundMortalityRate", $data[disease][infectious_mortality_rate]);
	$eclipse->setAttribute("diseaseName", $data[disease][name]);
	$eclipse->setAttribute("recoveryRate", $data[disease][infections_recovery_rate]);
	$eclipse->setAttribute("incubationRate",$data[disease][incubation_rate]);
	$eclipse->setAttribute("immunityLossRate", $data[disease][immunity_loss_rate]);
	$eclipse->setAttribute("transmissionRate", $data[disease][transmission_rate]);
	$eclipse->setAttribute("incubationRate", $data[disease][incubation_rate]);
	$dis_name = $data[disease][name];	
	$dublinCore->setAttribute("identifier", "platform:/resource/$n/decorators/$dis_name.standard");
	$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore" );
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/Identifiable" );
	

	$eclipse->appendChild($dublinCore);
	$doc->appendChild($eclipse);
	$doc->save($dir . "/decorators/$v.standard");
	
}

/**
* Function create_infector will create the infector object
* from $data.
* 
* @param dir The workspace directory.
**/
function create_infector ($dir) {
	$data = $_POST['data'];
	#create the infector object
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$eclipse = $doc->createElement("org.eclipse.stem.diseasemodels.standard:SIInfector");
	$dublinCore = $doc->createElement("dublinCore");
	$v = $data[infector][name];
	$n = $data[project_name];
	$eclipse->setAttribute("xmi:version","2.0");
	$eclipse->setAttribute("xmlns:xmi","http://www.omg.org/XMI");
	$eclipse->setAttribute("xmlns:org.eclipse.stem.diseasemodels.standard","http:///org/eclipse/stem/diseasemodels/standard.ecore");
	$eclipse->setAttribute("uRI","platform:/resource/$n/decorators/$v.standard");
	$eclipse->setAttribute("typeURI","stemtype://org.eclipse.stem/Identifiable");

	$eclipse->setAttribute("targetISOKey", $data[infector][location]);
	$eclipse->setAttribute("diseaseName", $data[disease][name]);
	$eclipse->setAttribute("populationIdentifier", $data[infector][population]);
	$eclipse->setAttribute("infectiousCount", $data[infector][abs_or_percent]);
	$inf_name = $data[infector][name];
	$dublinCore->setAttribute("identifier", "platform:/resource/$n/decorators/$inf_name.standard");
	$dublinCore->setAttribute("format", "http:///org/eclipse/stem/diseasemodels/standard.ecore" );
	$dublinCore->setAttribute("type", "stemtype://org.eclipse.stem/identifiable1129656" );
	$dublinCore->setAttribute("created", "");	

	$eclipse->appendChild($dublinCore);
	$doc->appendChild($eclipse);
	$doc->save($dir . "/decorators/$v.standard");
	
}

/**
* Function create_graphs will create the graphs and point to the 
* appropriate plugin.
* 
* @param dir The workspace directory.
**/
function create_graphs ($dir) {
	$data = $_POST['data'];
	$arr = array_unique($data[graphs]);
	foreach ($arr as $g) {
		$h = explode("geography/",$g);
		$path = STEM_ROOT_PATH . "/plugins/" . $h[1];
		if(strpos($g, "population")) {
			$j = explode("/", $g);
			$path = STEM_ROOT_PATH . "/plugins/population/human/data/country/$j[10]/$j[11]";
		}
		exec("cp $path $dir/graphs/"); 	
	}	
}

/**
* Function create_project will create the invisible .project file
* that is required by STEM due to eclipse.
*
* @param dir The workspace directory.
**/
function create_project ($dir) {
	$data = $_POST['data'];
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$project = $doc->createElement("projectDescription");
	$name = $doc->createElement("name", $data[project_name]);
	$comment = $doc->createElement("comment");
	$projects = $doc->createElement("projects");
	$buildSpec = $doc->createElement("buildSpec");
	$natures = $doc->createElement("natures");
	$nature = $doc->createElement("nature", "org.eclipse.stem.stemnature");
	$natures->appendChild($nature);
	$project->appendChild($name);	
	$project->appendChild($comment);	
	$project->appendChild($projects);	
	$project->appendChild($buildSpec);	
	$project->appendChild($natures);	
	$doc->appendChild($project);
	$doc->save($dir . "/.project");
}


$data = $_POST['data'];
$p_name = STEM_ROOT_PATH . "/workspace/" . $data[project_name];
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
create_project($p_name);
countryAutoLvl();

/**
* Function countryAutoLvl will use Wicked AutoMagick to find the XXX country and level.
* 
**/
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
        		$tmp = explode("_",$match[0]);
        		$lvl = $tmp[1];
			if ($lvl > $highLvl) $highLvl = $lvl;  
		}	
	}
	for ($i = $highLvl; $i > -1; $i--)
		if (file_exists(STEMVILLE_ROOT_PATH . "/rsrc/svg/" . $iso . "/" . $iso . "_" . $i . "_MAP.xml"))
		{
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
