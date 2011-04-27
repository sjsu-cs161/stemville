<?php

require_once('../backend/MongoClass.php');
$mc = new MongoClass();

?>
<div style="width: 90%; height:400px; margin: 50px auto 0 auto">
    <h2 style="text-align:center; margin: 10px">Select Scenario to Load</h2>
<div id="" style="text-align: center; margin-top: 30px">
    <select id="scen-select">
        <?php
        $scen = $mc->listAllRecords();
        for ($i=0; $i < count($scen); $i++) {
            echo '<option value="'.$scen[$i][project].'||'.$scen[$i][scenario].'">'.$scen[$i][scenario].'</option>';
        }
        ?>
    </select>
    <a class="button" id="btn_load-scen">Load Scenario</a>
</div>


<script type="text/javascript">
$('#btn_load-scen').click(function() {
    LOADER.load();
    var val         = $('#scen-select').val().split('||')
      , proj_name   = val[0]
      , scen_name   = val[1]
      ;
      
      console.log("proj_name:",proj_name);
      console.log("scen_name:",scen_name);
      
      scen = new StemVille.Scenario(proj_name, scen_name);
      
      scen.init(function() {
          console.log("loaaaded!!");
          NAV.show('simulate');
      })
});
</script>