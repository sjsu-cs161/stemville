/* 
 * StemVille library
 * core functionality -- requires jQuery, Raphael and ...
 * see README for usage
 */
 
 (function() { 
     
    var StemVille           = window.StemVille = {},
        BACKEND_MAPS        = "backend/rendermap.php",
        BACKEND_REGIONS     = "backend/regions.php",
        BACKEND_STEM        = "backend/runstem.php",
        BACKEND_OUTPUT      = "backend/getdata.php", //http://localhost/~bjorkstam/experimental/output/getdata.php?country=NOR&level=1&type=I&start=1&amount=100
        MAP_SCALE           = {x: 100, y: 100},                  // X x Y scale
        FETCH_DELAY         = 1000 * 10,             // delay between each output fetch
        OUTPUT_AMOUNT       = 100,                  // Number of iterations to fetch per request. Should be high; like 10-100
	CYCLE_KILL	    = 500, // Kill STEM after cycles fetched. Set to 0 for continuous run
        simObj              = {},
        graphObj            = {},
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
        
    var killSTEM = function() {
	$.getJSON('backend/killstem.php?pid='+this.PID, function(data) {
	    console.log("Killed STEM simulation -- all data loaded");
        });
    };
    
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
    var loadScenarioData = function() {     
        this.status.scenario_data = "loading data";
        // asynchronous load code follows
        var that = this;
        $.ajax({
            url: this.OPTIONS.BACKEND_SCENARIO_DATA || BACKEND_SCENARIO_DATA,
            data: "scenario="+this.scenario,
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
                that.status.scenario_data = "completed load";
                loadTracker.flag();
            }
         });
    };
    
    var outputHelper = function(type, ctx) {
        $.ajax({
            url: ctx.OPTIONS.BACKEND_OUTPUT || BACKEND_OUTPUT,
            data: "project="+ctx.project_name+"&scenario="+ctx.scenario+"&type="+type+"&level="+ctx.level+"&start="+(ctx.output[type].length+1)+"&amount="+(ctx.OPTIONS.OUTPUT_AMOUNT || OUTPUT_AMOUNT),
            timeout: 20000,
            success: function(data){
                var output = jQuery.parseJSON(data); 
                if (output.status === "success") {
                    for (var i=0; i < output.data.length; i++) {
                        ctx.output[type].push(output.data[i]);
                    };

        		    if (ctx.callbacks.load) {
        			    ctx.callbacks.load.call(ctx);
        		    };

                    if (output.data.length > (ctx.OPTIONS.OUTPUT_AMOUNT || OUTPUT_AMOUNT)-5) {
                        outputHelper(type, ctx);
                    };
                } else {
                    ctx.errors.push(output.msg);
                };
            }
         });
    };
    
    var loadOutput = function() {
        if (CYCLE_KILL > 0 && this.output.I.length >= CYCLE_KILL) {
	        this.stopLoad();
            return;
        }; 
        this.status.stem_output = "loading data";
        var that = this;
        $.getJSON('backend/checkstem.php?pid='+this.PID, function(data) {            
            if (data.status === "success") {
                for (type in that.output) (function(t) {
                    outputHelper(t, that);
                }(type));

                setTimeout(function() {
                    loadOutput.call(that);
                }, FETCH_DELAY);
            };
        });       
    };
    
    // Reset functions
    
    var resetMap = function() {
        var that = this;
        for (var region in this.mapData.regions) (function(region) {
            that.mapData.regions[region].attr({fill: that.mapData.colors[that.mapData.output], 'fill-opacity': 0.0});
        })(region);
    };
    
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
    
    var renderMap = function(cur_pos) {
        var that = this,
            map_regions = this.mapData.regions;
        
        for (var region in map_regions) (function(r) {
            var opacity = that.output[that.mapData.output][cur_pos][r] / 100000;
            opacity = (opacity > 1.0) ? 1.0 : opacity;
            map_regions[r].animate({'fill-opacity': opacity}, that.delay);
        })(region);
    };
    
    var renderGraph = function(cur_pos) {
        var that        = this;
        for (var i=0; i < this.graph.y.length; i++) (function(i) {
            var region = that.graph.y[i];
            that.graph.chart.series[i].addPoint(that.output[that.graph.output][cur_pos][region], cur_pos+1);
        } (i));
        
    };
    var simulation = function(cur_pos, max_pos, callback, ctx) {
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
            callback.call(ctx, cur_pos+1, max_pos);
        };
        
        if (++cur_pos < max_pos) {
            simObj[this.OBJECT_ID].SIM_ID = setTimeout(function() { simulation.call(that, cur_pos, max_pos, callback, ctx); }, this.delay);
        } else {
            delete simObj[this.OBJECT_ID].ITER;
            delete simObj[this.OBJECT_ID].RUNNING;
        };
    };
    
    StemVille.Scenario = function(project_name, scenario, country, level) {

        this.scenario = scenario;
        this.project_name = project_name;
        this.country = country;
        this.level = parseInt(level) || level;

        this.PID = null;
        
        this.OBJECT_ID = parseInt(Math.ceil(Math.random() * 1000000));
        
        this.errors = [];
        this.status = {
            regions: "not loaded",
            stem_output: "not loaded",
            map_data: "not loaded"
        };

        this.delay = 200;
        
	this.callbacks = {};
 
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
        
        // Graph (AKA Time Series)

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
        
        this.OPTIONS = {};
        this.OPTIONS.MAP_SCALE = {};
     };
     
    var sv_proto = StemVille.Scenario.prototype;
    
    sv_proto.setOptions = function(options) {
        this.OPTIONS = options;
        
        return this;
    };


    // Set a callback used when loading    
    sv_proto.setLoadCallback = function(callback) {
	this.callbacks.load = callback;
	
	return this;
    };
    // Callback for finished loading
    sv_proto.setLoadedCallback = function(callback) {
	this.callbacks.loaded = callback;

	return this;
    };

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
     
    sv_proto.setDelay = function(delay) {
        this.delay = delay;
        return this;
    };
     
     
    sv_proto.getErrors = function() {
        return this.errors;
    };
    
    sv_proto.getRegions = function() {
        return this.regions;
    }
    
    // Phase plot functionality
    
    sv_proto.setPhase = function(container, phase_1, phase_2, reg) {
        this.phaseContainer = container;

        this.phase = {
            data: [phase_1, phase_2],
            region: reg
        };

        return this;
    };
    
    sv_proto.hasPhase = function() {
        if (this.phaseContainer && this.phase.data && this.phase.region) return true;
        return false;
    };
    
    sv_proto.killPhase = function() {
        this.phaseContainer = null;
        
        return this;
    };
      
    // Graph functionality
      
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
    
      
    sv_proto.setGraphLabel = function(title, label_x, label_y) {
        this.graphLabel = {
            title: title || "STEM Output",
            x: label_x || this.graphLabel.x,
            y: label_y || this.graphLabel.y
        };
        
        return this;
    };

    sv_proto.hasGraph = function() {
        if (this.graphContainer && this.graph.y && this.graph.x) return true;
        return false;
    };

    sv_proto.killGraph = function() {
        this.graphContainer = null;

        return this;
    };

    sv_proto.initGraph = function() {
        if (this.hasGraph()) {
            resetGraph.call(this);
        };

        return this;
    };

    // Map functionality

    sv_proto.setMap = function(r_canvas, r_output) {
        this.mapData.canvas = r_canvas;

        this.mapData.output = r_output || "I";

        return this;
    };

    sv_proto.hasMap = function() {
        if (this.mapData.canvas && this.mapData.data) { return true; }
        return false;
    };

    sv_proto.killMap = function() {
        this.mapData.canvas = null;

        return this;
    };
    
    sv_proto.drawMap = function() {
        if (this.hasMap()) {
            this.mapData.canvas.clear();
            for (var region in this.mapData.data) {
                this.mapData.regions[region] = this.mapData.canvas.path(this.mapData.data[region]);
            };
        };
        
        return this;
    };
    
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
    
    sv_proto.run = function(callback, ctx) {
        // Make sure everything is reset first
        if (this.isRunning()) this.stop();
        if (this.hasGraph()) {
            resetGraph.call(this);
        };
        if (this.hasMap()) {
            resetMap.call(this);
        };        
        
        
        // Call centralized function to sync all animations
        simObj[this.OBJECT_ID].MAX_ITER = this.output.I.length;
        simObj[this.OBJECT_ID].CALLBACK = callback;
        simObj[this.OBJECT_ID].CTX = ctx || this;
        simObj[this.OBJECT_ID].RUNNING = true;

        simulation.call(this, 0, this.output.I.length, callback, ctx || this);
        
        return this;
    };

    
    
    sv_proto.pause = function(callback, ctx) {
        if (simObj[this.OBJECT_ID].SIM_ID) {
            clearTimeout(simObj[this.OBJECT_ID].SIM_ID);
        };
        
        return this;
    };
    
    sv_proto.resume = function() {
        if (!simObj[this.OBJECT_ID].SIM_ID) return this;
        var CUR_ITER = simObj[this.OBJECT_ID].ITER,
            MAX_ITER = simObj[this.OBJECT_ID].MAX_ITER,
            CALLBACK = simObj[this.OBJECT_ID].CALLBACK,
            CTX      = simObj[this.OBJECT_ID].CTX;
            
        simulation.call(this, CUR_ITER, MAX_ITER, CALLBACK, CTX);
        
        return this;
    };
    
    sv_proto.stop = sv_proto.reset = function() {
        if (simObj[this.OBJECT_ID].SIM_ID) {
            clearTimeout(simObj[this.OBJECT_ID].SIM_ID);
            simObj[this.OBJECT_ID] = {};
            var that = this;
            setTimeout(function() { resetMap.call(that); }, this.delay);
        };
        
        return this;
    };

    sv_proto.stopLoad = function() { 
        killSTEM.call(this);
        if (this.callbacks.loaded) {
            this.callbacks.loaded.call(this);
        };
        
        return this;
    }
    
    sv_proto.isRunning = function() {
        if (simObj[this.OBJECT_ID].RUNNING) return true;
        return false;
    };
    
    sv_proto.getIter = function() {
        return simObj[this.OBJECT_ID].ITER || 0;
    };

    sv_proto.getScenario = function() {
        return this.scenario;
    };
    sv_proto.getProjectName = function() {
        return this.project_name;
    };
    sv_proto.getCountry = function() {
        return this.country;
    };
    sv_proto.getLevel = function() {
        return this.level;
    };

    sv_proto.getDataTypes = function() {
        var out = [];
        for (var type in this.output) (function(type){ 
            out.push(type);
        }(type));

        return out;
    };
    sv_proto.getOutput = function(type) {
        type = type.toUpperCase();
        return this.output[type];
    };
    
 })();
