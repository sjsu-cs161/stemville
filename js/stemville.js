/* 
 * StemVille library
 * Author: Mikael Bjorkstam
 * - 2011 -
 *
 * core functionality -- requires jQuery and Raphael
 * see README for usage
 */
 
 (function() { 
    // Define global namespace StemVille and private variables 
    var StemVille           = window.StemVille = {},
        BACKEND_MAPS        = "backend/rendermap.php",
        BACKEND_REGIONS     = "backend/regions.php",
        BACKEND_STEM        = "backend/runstem.php",
        BACKEND_OUTPUT      = "backend/getdata.php",
        MAP_SCALE           = {x: 100, y: 100},                  // X x Y scale of map
        FETCH_DELAY         = 500,             // Delay waiting for STEM to start running before attempting data fetch
        OUTPUT_AMOUNT       = 100,                  // Number of iterations to fetch per request. Higher number = less requests
	    CYCLE_KILL	        = 500, // Kill STEM after cycles fetched. Set to 0 for continuous run
        simObj              = {},
        graphObj            = {},
        // Keeps track of loading and when callbacks should be executed
        loadTracker         = {
            level: 0,
            max_level: 7,
            callback: null,
            context: this,
            setCB: function(cb, ctx, max_level) {
                this.callback = cb;
                this.context = ctx;
                this.level = 0;
                this.max_level = max_level || 2;
            },
            flag: function () {
                if (++this.level === this.max_level && this.callback) {
                    this.callback.call(this.context);
                };
            }        
        };
    
    // Sends a request to the server to kill the current STEM process
        
    var killSTEM = function() {
	$.getJSON('backend/killstem.php?pid='+this.PID, function(data) {
	    console.log("Killed STEM simulation -- all data loaded");
        });
    };

    // Attempts to launch a new STEM process based on project name + scenario
    // If an attempt to launch fails, another will be attempted in 10s, etc.
    
    var loadSTEM = function() {
        var that = this;
        this.status.stem = "starting stem";
        $.ajax({
            url: this.OPTIONS.BACKEND_STEM || BACKEND_STEM,
            data: "project="+this.project_name+"&scenario="+this.scenario,
            timeout: 30000,
            success: function(data){
                var output = jQuery.parseJSON(data); 
                if (output.status === "success") {
                    that.PID = output.pid;
                    that.status.stem = "started stem";
                    loadOutput.call(that);
                } else {
                    that.status.stem = "could not start stem ... retrying soon";
                    that.errors.push(output.msg);
                    setTimeout(function() {
                        loadSTEM.call(that);
                    }, 1000 * 10)
                };
            }
         });
    };

    // Loads all regions for a scenario instance and stores them in the array regions
    var loadRegions = function() {
        this.status.regions = "loading data";
        var that = this;
        $.ajax({
            url: this.OPTIONS.BACKEND_REGIONS || BACKEND_REGIONS,
            data: "project="+this.project_name,
            timeout: 10000,
            success: function(data){
                var output = jQuery.parseJSON(data); 
                if (output.status === "success") {
                    that.regions = output.data;
                } else {
                    that.errors.push(output.msg);
                };
            },
            complete: function() {
                that.status.regions = "completed load";
                loadTracker.flag();
            }
         });
    };
    
    // Loads the map vector paths for a scenario instance
    var loadMap = function() {     
        this.status.map_data = "loading data";
        // asynchronous load code follows
        var that = this;
        $.ajax({
            url: this.OPTIONS.BACKEND_MAPS || BACKEND_MAPS,
            data: "project="+this.project_name+"&scale_x="+(this.OPTIONS.MAP_SCALE.x || MAP_SCALE.x)+"&scale_y="+(this.OPTIONS.MAP_SCALE.y || MAP_SCALE.y),
            timeout: 30000,
            success: function(data){
                var output = jQuery.parseJSON(data); 
                if (output.status === "success") {
                    that.mapData.data = output.data;
                } else {
                    that.errors.push(output.msg);
                };
            },
            complete: function() {
                that.status.map_data = "completed load";
                loadTracker.flag();
            }
         });
    };

    // Output helper method for internal use only
    // "Intelligently" loads STEM output into memory,
    // takes 2 params: type (S,E,I,R, etc.) and ctx (context)
    // loads at most OUTPUT_AMOUNT cycles at a time
    var outputHelper = function(type, ctx) {
        $.ajax({
            url: ctx.OPTIONS.BACKEND_OUTPUT || BACKEND_OUTPUT,
            data: "project="+ctx.project_name+"&scenario="+ctx.scenario+"&type="+type+"&level="+ctx.level+"&start="+(ctx.output[type].length+1)+"&amount="+(ctx.OPTIONS.OUTPUT_AMOUNT || OUTPUT_AMOUNT),
            timeout: 25000,
            success: function(data){
                var output = jQuery.parseJSON(data); 
                if (output.status === "success" && ctx.loading) {
                    for (var i=0; i < output.data.length; i++) {
                        ctx.output[type].push(output.data[i]);
                    };

        		    if (ctx.callbacks.load) {
        			    ctx.callbacks.load.call(ctx);
        		    };

                } else {
                    ctx.errors.push(output.msg);
                };
            },
            complete: function() {
                if (ctx.loading) {
                    outputHelper(type,ctx);
                };
            }
         });
    };

    // Internal method checking load status every second to ensure everything is in sync
    var checkLoadStatus = function() {

        if ((this.OPTIONS.CYCLE_KILL || CYCLE_KILL) === 0) {
            return;
        }

        if ((this.OPTIONS.CYCLE_KILL || CYCLE_KILL) > 0 && this.output.I.length >= (this.OPTIONS.CYCLE_KILL || CYCLE_KILL)) {
            this.stopLoad();
            return;
        } else {
            var that = this;
            setTimeout(function() {
                checkLoadStatus.call(that);
            }, 1000);
        }; 
    };
    // Internal method that is used to trigger outputHelper(...)
    // Makes sure that STEM is running before delegating load calls
    var loadOutput = function() {
        
        this.status.stem_output = "loading data";
        var that = this;
        $.getJSON('backend/checkstem.php?pid='+this.PID, function(data) {            
            if (data.status === "success") {
                for (type in that.output) (function(t) {
                    outputHelper(t, that);
                }(type));

                checkLoadStatus.call(that);

            } else {
                setTimeout(function() {
                    loadOutput.call(that);
                }, FETCH_DELAY);
            };
        });       
    };
    
    // Resets the map to default look (essentially removes any color left from simulation)
    var resetMap = function() {
        var that = this;
        for (var region in this.mapData.regions) (function(region) {
            that.mapData.regions[region].attr({fill: that.mapData.colors[that.mapData.output], 'fill-opacity': 0.0});
        })(region);
    };
    

    // Resets the graph -- basically kills all plotted data and performs internal cleaning
    // Restructures default behavior
    var resetGraph = function() {
        // Reset some stuff for animation later
        var that = this;
        $("#"+this.graphContainer).html('');
        graphObj[this.OBJECT_ID] = {};
        graphObj[this.OBJECT_ID].data = {};
        graphObj[this.OBJECT_ID].labels = [1,2,3,4,5];
        graphObj[this.OBJECT_ID].colors = {};
        var seriesObj = [];
        for (var i=0; i < this.graph.y.length; i++) (function (i) {
            graphObj[that.OBJECT_ID].data[that.graph.y[i]] = [];
            graphObj[that.OBJECT_ID].colors[that.graph.y[i]] = '#FF0000';
            seriesObj.push({name: that.graph.y[i], data: [] });
        }(i));
        
        this.graph.chart = new Highcharts.Chart({
             chart: {
                renderTo: that.graphContainer,
                defaultSeriesType: 'spline'
             },
             title: {
                text: that.graphLabel.title
             },
             xAxis: {
                categories: [],
                title: {
                    text: that.graphLabel.x
                 },
             },
             yAxis: {
                title: {
                   text: that.graphLabel.y
                }
             },
             plotOptions: {
                 spline: {
                     marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                     }
                 }
                
             },
             series: seriesObj
        });
    };
    
    // Simulation rendering
    // Takes current position (cycle/iteration) as parameter
    // Renders each region accordingly, based on output data    
    var renderMap = function(cur_pos) {
        var that = this,
            map_regions = this.mapData.regions;
        
        for (var region in map_regions) (function(r) {
            var opacity = that.output[that.mapData.output][cur_pos][r] / 100000;
            opacity = (opacity > 1.0) ? 1.0 : opacity;
            map_regions[r].animate({'fill-opacity': opacity}, that.delay);
        })(region);
    };
    
    // Graph rendering
    // Takes current position and pushes the right data onto the graph
    var renderGraph = function(cur_pos) {
        var that        = this;
        for (var i=0; i < this.graph.y.length; i++) (function(i) {
            var region = that.graph.y[i];
            that.graph.chart.series[i].addPoint(that.output[that.graph.output][cur_pos][region], cur_pos+1);
        } (i));
        
    };
    // Central simulation method
    // Keeps track of current cycle, max cycle and callbacks
    // Triggers callback every cycle, and triggers callback_finished after final cycle
    // The structure essentially enables pausing and resuming of a simulation
    var simulation = function(cur_pos, max_pos, callback, callback_finished) {
        var that = this;
        
        simObj[this.OBJECT_ID].ITER = cur_pos;
        // Call all functions that render something
        if (this.hasMap()) {
            renderMap.call(this, cur_pos);
        };
        if (this.hasGraph()) {
            renderGraph.call(this, cur_pos);
        };
        
        
        // Callback, with optional parameter current iteration and max iterations
        if (callback) {
            callback.call(this, cur_pos+1, max_pos);
        };
        
        if (++cur_pos < max_pos) {
            simObj[this.OBJECT_ID].SIM_ID = setTimeout(function() { simulation.call(that, cur_pos, max_pos, callback, callback_finished); }, this.delay);
        } else {
            if (callback_finished) {
                callback_finished.call(that);
            };
            delete simObj[this.OBJECT_ID].ITER;
            delete simObj[this.OBJECT_ID].RUNNING;
        };
    };
    

    // This is the core constructor of the global StemVille object
    // Used to build a new scenario object which bridges the gap between the absolute frontend and backend
    // Takes project_name, scenario, country and level as params and figures out the rest
    // Once executed, it generates the basic skeleton structure to be populated w/ data later on
    StemVille.Scenario = function(project_name, scenario, country, level) {

        this.scenario = scenario;
        this.project_name = project_name;
        this.country = country;
        this.level = parseInt(level) || level;

        this.PID = null;
        
        // random object id -- useful if an implementation allows several scenarios
        this.OBJECT_ID = parseInt(Math.ceil(Math.random() * 1000000));
        
        // logs errors and status messages based on data requests to the server
        this.errors = [];
        this.status = {
            regions: "not loaded",
            stem_output: "not loaded",
            map_data: "not loaded"
        };

        // default simulation delay
        this.delay = 200;
        
	    this.callbacks = {};
        
        // flag to keep track of output loading
        this.loading = true; 

        // tracks map svg paths 
        this.mapData = {
            canvas: null,
            output: "I",
            data: null,
            regions: {},
            colors: {
                "E": "#FFF000",
                "I": "#FF0000",
                "R": "#00FF00",
                "S": "#0000FF",
                "POP_COUNT": "#FF00DD",
                "INCIDENCE": "#FF00DD",
                "DISEASE_DEATHS": "#FF00DD"
            }
        };
        
        // Graph (AKA Time Series) data

        this.graphContainer = null;
        this.graph = {
            chart: null,
            output: "I",                   // I, E, R, S, POP_COUNT -- see this.output 
            x: "iteration",
            y: []
        };
        this.graphLabel = {
            title: "STEM Output",
            x: "time",
            y: "I"
        };
        
        // (currently unimplemented) skeleton to hold phase plot data
        this.phaseContainer = null;
        this.phase = {
            x: "time",
            data: null,
            region: null
        };
        this.phaseLabel = {
            title: "Phase Plot",
            x: "Time (days)",
            y: "Phase"
        }

        // Contains all output loaded from STEM 
        
        this.output = {
            "E": [],
            "I": [],
            "R": [],
            "S": [],
            "POP_COUNT": [],
            "INCIDENCE": [],
            "DISEASE_DEATHS": []
        };     
        this.regions = [];

        // Custom options that will override the global ones
        
        this.OPTIONS = {};
        this.OPTIONS.MAP_SCALE = {};
    };
    
    // The following functions extend the scenario object and will be accessible outside the current scope
    // and directly communicate with a specific scenario instance
    // All non-boolean commands are chainable

    var sv_proto = StemVille.Scenario.prototype;
    
    // Allows the user to provide an OPTIONS object that will override the global parameters
    // Mostly used during loading various kinds of data (such as backend urls, etc)
    sv_proto.setOptions = function(options) {
        this.OPTIONS = options;
        
        return this;
    };


    // Set a callback used when loading output data  
    sv_proto.setLoadCallback = function(callback) {
	this.callbacks.load = callback;
	
	return this;
    };

    // Set a callback to call when finished loading output data
    sv_proto.setLoadedCallback = function(callback) {
	this.callbacks.loaded = callback;

	return this;
    };

    // init(...) is a very important function that will trigger the current scenario instance
    // to start preparing itself by loading regions and map data from the server as well as executing STEM.
    // Additionally, init(...) lets the user provide a callback and context that will be executed when
    // everything has been loaded
    sv_proto.init = function(callback, ctx) { 
        if (callback) {
            loadTracker.setCB(callback, ctx ? ctx : this);
        };
        
        simObj[this.OBJECT_ID] = {};
           
        loadRegions.call(this);
        loadMap.call(this);
        //loadOutput.call(this);
        // start stem
        loadSTEM.call(this);
        
        return this;
    };
     
    // Setter for simulation delay
    sv_proto.setDelay = function(delay) {
        this.delay = delay;

        return this;
    };
     
    // Returns the error array which tracks any errors that may arise during load 
    sv_proto.getErrors = function() {
        return this.errors;
    };
    
    // returns an array with all regions
    sv_proto.getRegions = function() {
        return this.regions;
    }
    

    // Basic skeleton for phase plot functionality
    
    sv_proto.setPhase = function(container, phase_1, phase_2, reg) {
        this.phaseContainer = container;

        this.phase = {
            data: [phase_1, phase_2],
            region: reg
        };

        return this;
    };
    // boolean indicating whether a phase plot has been set up
    sv_proto.hasPhase = function() {
        if (this.phaseContainer && this.phase.data && this.phase.region) return true;
        return false;
    };
    
    // disattaches the phase plot functionality
    sv_proto.killPhase = function() {
        this.phaseContainer = null;
        
        return this;
    };
      
    // Graph functionality

    // Stores the graph container, what output data type as well as regions for y_axis
    // and optionally the x_axis (time by default--not recommended to change)
    sv_proto.setGraph = function(container, output, y_arr, x_axis) {
        this.graphContainer = container;

        this.graph = {
            output: output ? output : "I",
            y: y_arr,
            x: x_axis ? x_axis : this.graph.x
        };

        //resetGraph.call(this);
        
        return this;
    };
    
    // Sets the labels associated w/ the graph
    // Title, x-axis and y-axis
    sv_proto.setGraphLabel = function(title, label_x, label_y) {
        this.graphLabel = {
            title: title || "STEM Output",
            x: label_x || this.graphLabel.x,
            y: label_y || this.graphLabel.y
        };
        
        return this;
    };

    // boolean indicating whether graphing functionality is ready (setup) or not
    sv_proto.hasGraph = function() {
        if (this.graphContainer && this.graph.y && this.graph.x) return true;
        return false;
    };
    // disattaches graphing functionality
    sv_proto.killGraph = function() {
        this.graphContainer = null;

        return this;
    };

    // Used to initiate or do a "blank" render of the graph
    // Essentially it just accesses the internal resetGraph() method
    sv_proto.initGraph = function() {
        if (this.hasGraph()) {
            resetGraph.call(this);
        };

        return this;
    };

    // Map functionality

    // Stores the map canvas as well as data type (SEIR) to be used during simulation
    sv_proto.setMap = function(r_canvas, r_output) {
        this.mapData.canvas = r_canvas;

        this.mapData.output = r_output || "I";

        return this;
    };

    // boolean indicating whether the current simulation instance has graphing functionality
    sv_proto.hasMap = function() {
        if (this.mapData.canvas && this.mapData.data) { return true; }
        return false;
    };

    // Disattaches mapping from scenario instance
    sv_proto.killMap = function() {
        this.mapData.canvas = null;

        return this;
    };
    

    // Draws the plain map on the canvas if it has been set
    sv_proto.drawMap = function() {
        if (this.hasMap()) {
            this.mapData.canvas.clear();
            for (var region in this.mapData.data) {
                this.mapData.regions[region] = this.mapData.canvas.path(this.mapData.data[region]);
            };
        };
        
        return this;
    };
    

    // Resizes map
    // Currently the map resizing is done on the server for more efficiency
    sv_proto.resizeMap = function(new_scale, resize_canvas) {
        // Old code for 
        /*var map_regions = this.mapData.regions,
            scale_factor = new_scale/(this.OPTIONS.MAP_SCALE || MAP_SCALE);
        for (var region in map_regions) (function(r) {
            map_regions[r].scale(scale_factor, scale_factor, 0, 0);
        })(region);*/
        if (resize_canvas) {
            this.mapData.canvas.setSize(new_scale.x, new_scale.y);
        };
        
        this.OPTIONS.MAP_SCALE = new_scale;
        
        loadTracker.setCB(function() {
            this.drawMap();
        }, this, 1);
        
        loadMap.call(this);        
        
        return this;
    };
    
    
    // Simulation controls
    

    // Run triggers a simulation (starting from zero)
    // The user can provide two callbacks:
    // callback: executes after each iteration and receives two variables: cur_pos, max_pos
    // callback_finished: executes when a simulation has completed
    // Stops any running simulations before starting
    sv_proto.run = function(callback, callback_finished) {

        // Make sure everything is reset first
        if (this.isRunning()) this.stop();
        if (this.hasGraph()) {
            resetGraph.call(this);
        };
        if (this.hasMap()) {
            resetMap.call(this);
        };        
        
        
        // Call centralized function to sync all animations
        simObj[this.OBJECT_ID].MAX_ITER          = this.output[this.mapData.output].length;
        simObj[this.OBJECT_ID].CALLBACK          = callback;
        simObj[this.OBJECT_ID].CALLBACK_FINISHED = callback_finished;
        simObj[this.OBJECT_ID].RUNNING           = true;

        simulation.call(this, 0, this.output[this.mapData.output].length, callback, callback_finished);
        
        return this;
    };

    
    // Pauses a simulation. The current state is automagically stored in memory already
    sv_proto.pause = function() {
        if (simObj[this.OBJECT_ID].SIM_ID) {
            clearTimeout(simObj[this.OBJECT_ID].SIM_ID);
        };
        
        return this;
    };
    
    // Resumes a paused scenario by retrieving all necessary data from memory
    sv_proto.resume = function() {
        if (!simObj[this.OBJECT_ID].SIM_ID) return this;
        var CUR_ITER          = simObj[this.OBJECT_ID].ITER,
            MAX_ITER          = simObj[this.OBJECT_ID].MAX_ITER,
            CALLBACK          = simObj[this.OBJECT_ID].CALLBACK,
            CALLBACK_FINISHED = simObj[this.OBJECT_ID].CALLBACK_FINISHED;
            
        simulation.call(this, CUR_ITER, MAX_ITER, CALLBACK, CALLBACK_FINISHED);
        
        return this;
    };
    
    // stop() and reset() are bound to the same function
    // Essentially, they stop the current simulation and reset information
    // that may be stored in memory about it (current cycle, max cycle, etc)
    sv_proto.stop = sv_proto.reset = function() {
        if (simObj[this.OBJECT_ID].SIM_ID) {
            clearTimeout(simObj[this.OBJECT_ID].SIM_ID);
            simObj[this.OBJECT_ID] = {};
            var that = this;
            setTimeout(function() { resetMap.call(that); }, this.delay);
        };
        
        return this;
    };
    // stopLoad() stops the loading of STEM output data and kills the STEM process
    sv_proto.stopLoad = function() { 
        killSTEM.call(this);
        this.loading = false;
        if (this.callbacks.loaded) {
            this.callbacks.loaded.call(this);
        };

        return this;
    }
    
    // boolean indicating whether a simulation is currently running
    sv_proto.isRunning = function() {
        if (simObj[this.OBJECT_ID].RUNNING) return true;
        return false;
    };
    
    // Returns the current iteration (if running) or 0 (not running)
    sv_proto.getIter = function() {
        return simObj[this.OBJECT_ID].ITER || 0;
    };

    // Returns the scenario name
    sv_proto.getScenario = function() {
        return this.scenario;
    };

    // Returns the project name
    sv_proto.getProjectName = function() {
        return this.project_name;
    };
    // Returns the country
    sv_proto.getCountry = function() {
        return this.country;
    };
    // Returns the country level
    sv_proto.getLevel = function() {
        return this.level;
    };

    // Returns all possible data types (S,E,I,R, etc..)
    sv_proto.getDataTypes = function() {
        var out = [];
        for (var type in this.output) (function(type){ 
            out.push(type);
        }(type));

        return out;
    };
    // Returns all the available output of a specific data type (S,E,I,R, etc..)
    sv_proto.getOutput = function(type) {
        type = type.toUpperCase();
        return this.output[type];
    };
    
 })();
