stemville.js
============
JavaScript library to be used in the StemVille web-application. Creates a global namespace **StemVille**

Usage
-----
To create a new scenario, use the constructor **StemVille.Scenario(_projectName_, _scenarioName_, _country_, _level_)**:

    var scen = new StemVille.Scenario(244012012, 'TestScenario', 'SWE', 1);


There are some default properties for loading data in the library, such as *BACKEND\_URL*. These can be overriden with a custom **options** object which can contain any amount of the parameters. All properties provided in this options object will override the default settings. **setOptions(_options_)**:

	scen.setOptions({MAP_SCALE: {x: 500, y: 300}}); // Will retrieve the map data with scale to fit 500x300 region

The following properties can be overridden by *setOptions(...)*: *BACKEND\_MAPS*, *BACKEND\_REGIONS*, *BACKEND\_OUTPUT*, *MAP\_SCALE*, *OUTPUT\_AMOUNT*, *BACKEND\_STEM*, *CYCLE\_KILL*.


In order to load regions and map data, you must initiate the object by calling **init(_callback_, _context_)** *(callback and context are optional)*. **init** will also trigger STEM to run headlessly and start logging output, as well as kickstarting processes that will attempt to fetch this output.

If you provide a callback, that function will fire when map and regions are loaded. In addition, you can specify a custom context which _this_ will refer to.

    scen.init();
	// Alternatively:
	scen.init(function() { alert("now everything is loaded!") }); // The alert box will display once the data load is complete

This will load all data required and prepare the object. By allowing a callback function, we can disable some user controls until all data has finished loading and the object is populated with necessary data.

While there is a lot of data to be loaded, our scenario-object contains a *status* object indicating load status for each module.

	scen.status;

There is also a way to check for possible errors that may occur when loading the data. These errors will stack up in an array and can be accessed by **getErrors()**

	scen.getErrors(); // may return [] indicating no errors
	
	scen.getErrors() // may also return ['some error', 'some other error', ...]

If you wish to draw a map, you must specify a raphael canvas to be drawn on.

	scen.setMap(r_canvas);
	
	// The following returns a boolean
	scen.hasMap(); // true
	
	// Also, you can reset and remove the map feature by calling
	scen.killMap();
	
	scen.hasMap(); // false
	
	scen.drawMap(); // draws the map on the canvas
	
	scen.resizeMap({x: 500, y:300}); // Redraws to fit 500x300px area
	scen.resizeMap({x: 500, y:300}, true); // Redraws map to fit 500x500px and resizes the raphael canvas


In order to define a graph, you must specify a graph-container, the STEM output value (I, E, R, S, etc.) and an array of regions to be drawn in the y-axis. As an optional parameter, you can specify the x-axis (_iteration_ by default).

	scen.setGraph('graph-container', "I", ['region1', 'region2', ...]);
	
	// The following returns a boolean
	scen.hasGraph(); // true
	
	// Also, you can reset and remove the graphing feature by calling
	scen.killGraph();
	
	scen.hasGraph(); // false

You can also add custom title and axes labels to the graph **setGraphLabel(_title_, _x\_axis_, _y\_axis_)**,

	scen.setGraphLabel('STEM Output', 'time', 'incidence rate');

If you wish to override the default delay used to render graphs and maps, you can do so by calling **setDelay(_delay_)**

	scen.setDelay(200);


An array of valid regions for the current object can be obtained,

	scen.getRegions(); // returns ['region-1', 'region-2', ...]


If you wish to run a scenario, you can call **run()**. Alternatively, you can provide two callbacks**run(_callback_, _callback\_finished_)**. Your callback function may contain two parameters that will represent the current iteration as well as max iteration.

	scen.run(); // no callback
	
	scen.run(function(iter, max) {
		console.log("current iteration:", iter);	// will print the current iteration to the console
	});

	scen.run(function(iter,max) {
		console.log('current status:',100*iter/max,'%');
	}, function() {
		console.log('this will execute when simulation finishes')
	});

Pausing any time during runtime can be achieved by

	scen.pause();
	
	// then resume again
	scen.resume();

A scenario can be stopped (and reset) by calling **stop()**

	scen.stop();
	
	// also possible
	scen.reset();

The current iteration can be retrieved by calling

	scen.getIter();

In addition, should you wish to determine whether your program is currently running a simulation (paused or not),

	scen.isRunning(); // returns either true or false

Additionally, each scenario instance has many getters and setters that may be useful in various scenarios:

	scen.stopLoad(); // cancels loading and terminates STEM process
	scen.getScenario(); // returns scenario name
	scen.getProjectName(); // returns project name
	scen.getCountry(); // returns country
	scen.getLevel(); // returns level

	scen.getDataTypes(); // returns all available data types (S,E,I,R, etc)
	scen.getOutput("I"); // returns all output for data type "I"

All functions are chain-able, which means you can do

    scen.init().setMap(r_canvas).setDelay(150).run();

