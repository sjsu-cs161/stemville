<!DOCTYPE html>
<html>
	<head>
		<title>StemVille Prototype</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
		<script src="js/raphael-min.js" type="text/javascript" charset="UTF-8"></script>
        <script src="js/raphael-zpd.js" type="text/javascript" charset="UTF-8"></script>
        <script src="plugins/highcharts/highcharts.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/stemville.js"></script>
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript">
    	// Define links, bind them live so the event listener will always work even if content is replaced/updated
    	
    	$('#menu_create').live('click', function() {
    	    NAV.show('create');
    	});
    	$('#menu_load').live('click', function() {
    	    NAV.show('load');
    	});
    	$('#stemville_logo').live('click', function() {
    	    NAV.show('front');
    	});
    	$('#scen_show').live('click', function() {
    		NAV.show('scenario');
    	});
    	
		</script>
		
		<link rel="stylesheet" media="screen" href="css/main.css"/>
		<link rel="stylesheet" media="screen" href="css/custom-theme/jquery-ui-1.8.11.custom.css"/>
		<style>
		a.button {
		    color: #fff;
		}
		#accordion form p, div.finalize > p {
		    clear:both;
		    margin: 6px 0;
		}
		#accordion form p label {
		    width: 220px;
		    display:block;
		    text-align:right;
		    float:left;
		    margin-right: 5px;
		}
		#accordion form p input {
		    float:left;
		}
		</style>
	</head>
	<body>
		<div id="header">
		    <div id="header_content">
		        <div class="left">
    		        <img src="img/stemville.png" id="stemville_logo" />
    		    </div>
    		    <div class="right" id="menu">
    		        <div id="menu_create">Design Scenario</div>
    		        <div id="menu_load">Load Scenario</div>
    		        <div id="scen_show" style="display: none">Current Scenario</div>
    		    </div>
    		</div>
		</div>
		<div id="loader" style="display:none">
		    <div>
		        <img src="img/loader.gif" /><br/>
		        <p>LOADING</p>
		    </div>
		</div>
		<div id="content">
		    <div class="c-tab" id="front_page">
		        <div style="position:relative; text-align:center; padding-top:15%; color: #fff; text-shadow: 1px 1px 3px #444">
		            <h1 style="font-size:100px">Welcome!</h1>
		            <h2 style="font-size: 35px">STEM in the <em>cloud</em></h2>
		            <h3 style="margin-top:10px">Use the <strong>menu</strong> to get started!</h3>
		        </div>
		    </div>
		    <div class="c-tab blank" id="new_scenario_page" style="display:none">
		        <div>
		            <h1 style="font-size:100px">Welcome!</h1>
		            <h2 style="font-size: 35px">load a new scenario</h2>
		            <h3 style="margin-top:10px">(hint: use the top-left menu)</h3>
		        </div>
		    </div>
		    <div class="c-tab" id="load_scenario_page" style="display:none">
		        <div>
		            <h1 style="font-size:100px">Load scenario!</h1>
		        </div>
		    </div>
		    <div class="c-tab" id="cur_scenario_page" style="display:none">
		        <div style="position:relative; text-align:center; padding-top:15%; color: #fff; text-shadow: 1px 1px 3px #444">
		            <h1 style="font-size:100px">Current scenario...</h1>
		        </div>
		    </div>
		    <div class="c-tab" id="set_scenario_page" style="display:none">
		        <div style="position:relative; text-align:center; padding-top:15%; color: #fff; text-shadow: 1px 1px 3px #444">
		            <h1 style="font-size:100px">Settings for current scenario</h1>
		        </div>
		    </div>
            <div class="c-tab blank" id="simulation_page" style="display:none">
                <div style="height: 100%;"></div>
            </div>
		</div>
	</body>
</html>
