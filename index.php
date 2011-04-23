<!DOCTYPE html>
<html>
	<head>
		<title>StemVille Prototype</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
		<script src="maps/raphael-min.js" type="text/javascript" charset="UTF-8"></script>
        <script src="highcharts/highcharts.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/stemville.js"></script>
		<script type="text/javascript">
		
		window.onload = function() {
		    
    		(function() {
    		    var loading = false
    		     ,  LOADER = window.LOADER = {};
    		     LOADER.load = function() {
    		         loading = true;
    		         $('#loader').show();
    		     }
    		     LOADER.unload = function() {
    		         loading = false;
    		         $('#loader').hide();
    		     }
    		     LOADER.toggle = function() {
    		         loading ? this.unload() : this.load();
    		     }
    		}());
		
		
    		(function() {
    		    /**
        		 * Please define all pages correctly below to enable a smooth navigation system.
        		 * All pages must be valid elements which have class="c-tab" defined and valid ID.
        		 * Make sure they are nested properly or major messups will occur
        		 */
    		 
    		    var pages           = $('.c-tab')
    		     ,  page_front      = $("#front_page")
    		     ,  page_create     = $("#new_scenario_page")
    		     ,  page_load       = $("#load_scenario_page")
    		     ,  page_scenario   = $("#cur_scenario_page")
    		     ,  page_settings   = $("#set_scenario_page")
    		     ,  loaded_create   = false                    // Flag to indicate whether a user is creating a scenario for the first time
    		     ,  NAV             = window.NAV = {};
		     
    		     NAV.show = function(which_page) {
    		         // First set the loader
    		         LOADER.load();
    		         // Then we hide *everything*
    		         pages.hide();
    		         // Then we do something else
		         
    		         if (which_page === 'create') {
    		             // Load create page for a new scenario
    		             // For first time load, ajax load the page _scenario.html
    		             if (!loaded_create) {
    		                 $.ajax({
                                 url: "partials/_scenario.html",
                                 timeout: 10000,
                                 success: function(data){
                                     page_create.find("> div").html(data);
                                     loaded_create = true;
                                     page_create.show();
                                 },
                                 complete: function() {
                                     LOADER.unload();
                                 }
                              });
    		             } else {
    		                 page_create.show();
    		                 LOADER.unload();    		                 
    		             }
    		         } else if (which_page === 'load') {
    		             // Display the page where a user can laod a scenario
    		             page_load.show();
    		         } else if (which_page === 'scenario') {
    		             // Display current scenario workflow
    		             page_scenario.show();
    		         } else if (which_page === 'settings') {
    		             // Display settings for current scenario
    		             page_settings.show();
    		         } else {
    		             // Show the front page:
    		             page_front.show();
    		         }
		         
    		         // All laoding has been done, now unload
    		         if (which_page !== 'create') {
    		             setTimeout(function() { LOADER.unload(); }, 800);
    		         }
		        
    		     }
    		}());
    	}
    	
    	
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
    	
		</script>
		<!--<link rel="stylesheet" media="screen" href="css/main.css"/>-->
	    <style>
	    * {
	        margin:0;
	        padding:0;	        
	    }
	    html,body {
	        width: 100%;
	        height: 100%;
	        line-height:1;
	        font-size:14px;
	        font-family: "Trebuchet MS", Verdana, Arial;
	    }
	    #header {
	        position:fixed;
	        left:0;
	        right:0;
	        top:0;
	        height: 42px;
	        line-height:42px;
	        background: url(img/header_bg.png);
	        border-bottom: 2px solid #0f4c80;
	        
	        color: #eee;
	    }
	    #header_content {
	        margin: 0 20px;
	        height:42px;
	    }
	    #header_content > div:first-child > img {
	        margin-top:5px;
	    }
	    
	    #header_content > div + div {
	        margin-left: 20px;	        
	    }
	    #menu {
	        font-size:16px;
	        font-weight:bold;
	        text-shadow: 1px 1px 1px #444;
	    }
        #menu > div {
            padding: 0 5px;
            cursor: pointer;
            float:left;
        }
        #menu > div:hover {
            background: rgba(255,255,255,.2);
        }
        #menu > div + div{
            margin-left: 20px;
        }
	    
	    #content, #loader {
	        position:fixed;
	        overflow:auto;
	        top:44px;
	        left:0;
	        right:0;
	        bottom:0;
	        background: #eee;
	    }
	    #loader {
	        top:0;
	        z-index: 999;
	        background: rgba(0,0,0,.75);
	    }
	    #loader > div {
	        position:relative;
	        padding-top:20%;
	        font-size: 24px;
	        color: #fff;
	        text-shadow: 1px 1px 3px #000;
	        margin:0 auto;
	        text-align: center;
	    }
	    .left {
	        float: left;
	    }
	    .right {
	        float: right;
	    }
	    .c-tab {
	        width:100%;
	        height:100%;
	        overflow:auto;
	        background: #eee;
	        background-image: -webkit-gradient(
                linear,
                left bottom,
                left top,
                color-stop(0, rgb(204,204,204)),
                color-stop(1, rgb(255,255,255))
            );
            background-image: -moz-linear-gradient(
                center bottom,
                rgb(204,204,204) 0%,
                rgb(255,255,255) 100%
            );
	    }
	    .c-tab > div {
	        position:relative;
	  

	    }
	    .blank {
	        background: #fff;
	    }
	    #front_page {
	        background-image: -webkit-gradient(
                linear,
                left bottom,
                left top,
                color-stop(0, rgb(31,94,171)),
                color-stop(1, rgb(171,221,255))
            );
            background-image: -moz-linear-gradient(
                center bottom,
                rgb(31,94,171) 0%,
                rgb(171,221,255) 100%
            );
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
		        <div style="position:relative; text-align:center; padding-top:15%; color: #fff; text-shadow: 1px 1px 3px #444">
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
		</div>
	</body>
</html>