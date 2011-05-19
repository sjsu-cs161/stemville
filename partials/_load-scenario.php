<?php
// This file represents the *LOAD SCENARIO* screen

// Load the mongodb handlers
require_once('../backend/MongoClass.php');
// Create a new instance of MongoClass
$mc = new MongoClass();

?>
<div style="width: 90%; height:400px; margin: 50px auto 0 auto">
    <h2 style="text-align:center; margin: 10px">Select Scenario to Load</h2>
<div id="" style="text-align: center; margin-top: 30px">
    <select id="scen-select">
        <?php
        // Poll all records from the database
        $scen = $mc->listAllRecords();
        for ($i=0; $i < count($scen); $i++) {
            echo '<option value="'.$scen[$i][project].'|||'.$scen[$i][scenario].'|||'.$scen[$i][maps][0][country].'|||'.$scen[$i][maps][0][level].'">'.$scen[$i][scenario].' ('.$scen[$i][maps][0][country].'_'.$scen[$i][maps][0][level].')</option>';
        }
        ?>
    </select>
    <a class="button" id="btn_load-scen">Load Scenario</a>
    <div style="margin-top: 100px">
	<a class="button" id="clear_processes">Kill All Running STEM Processes</a>
    </div>
</div>


<script type="text/javascript">

$('#clear_processes').click(function() {
    // Kills all STEM processes on the server and cleans out all output data
    $.get('backend/cleanrun.php');
});

$('#btn_load-scen').click(function() {
    // Display load screen
    // Figure out what the user selected
    // Create new Scenario instance and redirect to simulation screen
    LOADER.load();
    var val         = $('#scen-select').val().split('|||')
      , proj_name   = val[0]
      , scen_name   = val[1]
      , country     = val[2]
      , level       = val[3]
      ;
      
      console.log("proj_name:",proj_name);
      console.log("scen_name:",scen_name);
      
      scen = new StemVille.Scenario(proj_name, scen_name, country, level);
      
      scen.init(function() {
          console.log("loaaaded!!");
          NAV.show('simulate');
      })
});
</script>
