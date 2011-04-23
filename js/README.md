stemville.js
============
JavaScript library to be used in the StemVille web-application. Creates a global object **StemVille**, 

*This documentation is a work in progress and may change. The JavaScript library is currently incomplete and will be uploaded when it is a bit more substantial.*

Usage
-----
To create a new (pre-defined) scenario:

    var sau = new StemVille.Scenario('SAU', 2, 'Influenza');

The last parameter is optional (_Influenza_ by default)

There are some default properties for loading data in the library, such as *MAP\_SCALE* and *BACKEND\_URL*. These can be overriden with a custom **options** object which can contain any amount of the parameters. All properties provided in this options object will override the default settings. **setOptions(_options_)**:

	sau.setOptions({MAP_SCALE: 300}); // Will retrieve the map data with scale 300

The following properties can be set: *BACKEND\_MAPS*, *BACKEND\_REGIONS*, *BACKEND\_OUTPUT*, *MAP\_SCALE*, *OUTPUT\_AMOUNT*.


In order to load regions and data, you must initiate the object by calling **init(_callback_, _context_)** *(callback and context are optional)*

If you provide a callback, that function will fire when everything is loaded. In addition, you can specify a custom context which _this_ will refer to.

    sau.init();
	// Alternatively:
	sau.init(function() { alert("now everything is loaded!") }); // The alert box will display once the data load is complete

This will load all data required and prepare the object. By allowing a callback function, we can disable some user controls until all data has finished loading and the object is populated with necessary data.

While there is a lot of data to be loaded, our scenario-object contains a *status* object indicating load status for each module.

	sau.status;

There is also a way to check for possible errors that may occur when loading the data. These errors will stack up in an array and can be accessed by **getErrors()**

	sau.getErrors(); // may return [] indicating no errors
	
	sau.getErrors() // may also return ['some error', 'some other error', ...]

If you wish to draw a map, you must specify a raphael canvas to be drawn on.

	sau.setMap(r_canvas);
	
	// The following returns a boolean
	sau.hasMap(); // true
	
	// Also, you can reset and remove the map feature by calling
	sau.killMap();
	
	sau.hasMap(); // false
	
	sau.drawMap(); // draws the map on the canvas
	
	sau.resizeMap(200); // Redraws to fit 200x200px area
	sau.resizeMap(500, true); // Redraws map to fit 500x500px and resizes the raphael canvas


In order to define a graph, you must specify a graph-container, the STEM output value (I, E, R, S, POP\_COUNT) and an array of regions to be drawn in the y-axis. As an optional parameter, you can specify the x-axis (_iteration_ by default).

	sau.setGraph('graph-container', "I", ['region1', 'region'2, ...]);
	
	// The following returns a boolean
	sau.hasGraph(); // true
	
	// Also, you can reset and remove the graphing feature by calling
	sau.killGraph();
	
	sau.hasGraph(); // false

You can also add custom title and axes labels to the graph **setGraphLabel(_title_, _x\_axis_, _y\_axis_)**,

	sau.setGraphLabel('STEM Output', 'time', 'incidence rate');

If you wish to override the default delay used to render graphs and maps, you can do so by calling **setDelay(_delay_)**

	sau.setDelay(200);

For the phase plot feature, you can

	sau.setPhase('phase-graph-container', "I", "R", "region-4");
	sau.hasPhase(); // returns true
	
	sau.killPhase();
	sau.hasPhase(); // returns false

An array of valid regions for the current object can be obtained,

	sau.getRegions(); // returns ['region-1', 'region-2', ...]


If you wish to run a scenario, you can call **run()**. Alternatively, you can provide a callback and an optional context, **run(_callback_, _context_)**. Your callback function may contain a parameter that will represent the current iteration.

	sau.run(); // no callback
	
	sau.run(function(iter) {
		console.log("current iteration:", iter);	// will print the current iteration to the console
	});

Pausing any time during runtime can be achieved by

	sau.pause();
	
	// then resume again
	sau.resume();

A scenario can be stopped (and reset) by calling **stop()**

	sau.stop();
	
	// also possible
	sau.reset();

The current iteration can be retrieved by calling

	sau.getIter();

In addition, should you wish to determine whether your program is currently running a simulation (paused or not),

	sau.isRunning(); // returns either true or false

All functions are chain-able, which means you can do

    sau.init().setMap(r_canvas).setDelay(150).run();

