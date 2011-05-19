/*
 * utils.js is used for various tasks directly affecting the UI
 * It provides core functionality for navigation as well as other helpers
 */

var scen;

// Attach an onload handler that enables a LOAD screen
// Creates global object LOADER which can be used to set or unset the load screen
window.onload = function() {
    
	(function() {
	    var loading = false
	     ,  LOADER = window.LOADER = {};

       // display load screen
	     LOADER.load = function() {
	         loading = true;
	         $('#loader').show();
	     }
       // hide load screen
	     LOADER.unload = function() {
	         loading = false;
	         $('#loader').hide();
	     }

       // Automatic toggle of load screen.
       // Hide if visible, show if not
	     LOADER.toggle = function() {
	         loading ? this.unload() : this.load();
	     }
	}());


	(function() {
	  /**
     * The following code enables easy page navigation using ajax load of partial HTML files
     * Defines one global namespace: NAV
     * 
		 * Please define all pages correctly below to enable a smooth navigation system.
		 * All pages must be valid elements which have class="c-tab" defined and valid ID.
		 * Make sure they are nested properly or major messups will occur
		 */
	 
	    var pages           = $('.c-tab')
	     ,  page_front      = $("#front_page")
	     ,  page_create     = $("#new_scenario_page")
	     ,  page_load       = $("#load_scenario_page")
	     ,  page_scenario   = $("#cur_scenario_page")
       ,  page_simulation = $("#simulation_page")
	     ,  loaded_create   = false                    // Flag to indicate whether a user is creating a scenario for the first time
	     ,  NAV             = window.NAV = {};
     
       // The show function is used as a router dealing w/ partial page loads
       // as well as making sure to display the correct view (page) to the user
	     NAV.show = function(which_page) {
	         // First set the loader
	         LOADER.load();
	         // Then we hide *everything*
	         pages.hide();
	         // Then we do something else
            
           // load *create scenario* page
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
                             $("#accordion").accordion({autoHeight: false});
                         },
                           complete: function() {
                               LOADER.unload();
                           }
                      });
	             } else {
	                 page_create.show();
	                 LOADER.unload();    		                 
	             }
           // load *load scenario* page
	         } else if (which_page === 'load') {
	             $.ajax({
                      url: "partials/_load-scenario.php",
                      timeout: 10000,
                      success: function(data){
                          page_load.find("> div").html(data);
                          page_load.show();
                      },
                      complete: function() {
                          LOADER.unload();
                      }
                   });
            // load *simulation* page
            } else if (which_page === 'simulate') {
	             $.ajax({
                      url: "partials/_simulation.html",
                      timeout: 10000,
                      success: function(data){
                          page_simulation.find("> div").html(data);
                          page_simulation.show();
                      },
                      complete: function() {
                          simulationLoaded();
                          $.getJSON('rsrc/json/flags.json', function(data) {
                              var file = '<img style="height:30px; float: left; padding-top:6px; margin-right:6px;" src="rsrc/flags/'+data[scen.getCountry()]+'"/>';
                              $('#scen_show').html(file+' '+ scen.getScenario()).show();
                          });
                          LOADER.unload();
                      }
                   });
           // No loading, just display currentscenario page
	         } else if (which_page === 'scenario') {
	             // Display current scenario workflow
	             page_simulation.show();
	         } else {
	             // Show the front page:
	             page_front.show();
	         }
         
	         // All loading has been done, now unload
	         if (which_page !== 'create' && which_page !== 'load') {
	             setTimeout(function() { LOADER.unload(); }, 800);
	         }
        
	     }
	}());
}

/**
 * Creating objects
 * Code found online: serializes a form into JS object
 */
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

// ******************************************************
// THE FOLLOWING CODE IS USED WITH *CREATE SCENARIO* PAGE
// ******************************************************


// Set status
function setStatus(status) {
    $('#status').html(status);
}

// TMP_MODELS is the core/root models object
var TMP_MODELS;

// Recursive function to insert a model into a model
var insertIntoModel = function(arrModels, parent, model) {
    if (arrModels.length === 0) {
        return;
    }
    for (var i = 0; i < arrModels.length; i++) {
        if (arrModels[i].name === parent) {
            arrModels[i].models.push(model);
            return;
        } else {
            insertIntoModel(arrModels[i].models, parent, model);
        }
    }
}
// Recursive function to insert a graph into a model
var insertGraphsIntoModel = function(arrModels, parent, graphs) {
    if (arrModels.length === 0) {
        return;
    }
    for (var i = 0; i < arrModels.length; i++) {
        if (arrModels[i].name === parent) {
            if (!arrModels[i].graphs) {
                arrModels[i].graphs = graphs;
                return;
            } else {
                arrModels[i].graphs = arrModels[i].graphs.concat(graphs);
                return;
            }            
        } else {
            insertGraphsIntoModel(arrModels[i].models, parent, graphs);
        }
    }
}
// Recursive function to insert an infector into a model
var insertInfectorIntoModel = function(arrModels, parent, infector) {
    if (arrModels.length === 0) {
        return;
    }
    for (var i = 0; i < arrModels.length; i++) {
        if (arrModels[i].name === parent) {
            arrModels[i].infector = infector;
            return;        
        } else {
            insertInfectorIntoModel(arrModels[i].models, parent, infector);
        }
    }
}
// Recursive function to insert a disease into a model
var insertDiseaseIntoModel = function(arrModels, parent, disease) {
    if (arrModels.length === 0) {
        return;
    }
    for (var i = 0; i < arrModels.length; i++) {
        if (arrModels[i].name === parent) {
            arrModels[i].disease = disease;
            return;        
        } else {
            insertDiseaseIntoModel(arrModels[i].models, parent, disease);
        }
    }
}

// Global graph object
var GLOBAL_GRAPHS = [];

// Add one or multiple graphs to a model as well as global_graphs
var addGraphs = function() {
    var graphs = $('#selectable_graphs').val();
    var model = $('#graphs_model').val();
    insertGraphsIntoModel([TMP_MODELS], model, graphs);
    GLOBAL_GRAPHS = GLOBAL_GRAPHS.concat(graphs);
    
    graphs.forEach(function(graph) {
        $('#graphs_status').append('<p>*** Added <em>'+ graph +'</em> to <strong>'+ model +'</strong></p>');
    });
    
}

// Generate a model (triggered when user add model in form)
// Creates the correct structure and outputs new nesting options
var generateModel = function() {
    var model = $('#frm-models').serializeObject();
    var first = false;
    if (!TMP_MODELS) {
        TMP_MODELS = model;
        delete TMP_MODELS.parent;
        TMP_MODELS.models = [];
        TMP_MODELS.graphs = [];
        first = true;
    } else {
        var parent = model.parent;
        delete model.parent;
        model.models = [];
        model.graphs = [];
        
        if (TMP_MODELS.name === parent) {
            TMP_MODELS.models.push(model);
        } else {
            insertIntoModel(TMP_MODELS.models, parent, model);
        }
    }
    if (first) {
        $('#frm-models').find('select[name="parent"]').html('<option value="'+model.name+'">'+model.name+'</option>');
        $('#graphs_tab').find('select[id="graphs_model"]').html('<option value="'+model.name+'">'+model.name+'</option>');
        $('#frm-disease').find('select[name="disease_model"]').append('<option value="'+model.name+'">'+model.name+'</option>');
        $('#frm-infector').find('select[name="infector_model"]').append('<option value="'+model.name+'">'+model.name+'</option>');
        
        $('#graphs_tab').show();
        $('#pre_graphs_tab').hide();
    } else {
        $('#frm-models').find('select[name="parent"]').append('<option value="'+model.name+'">'+ parent + '/' + model.name+'</option>');
        $('#graphs_tab').find('select[id="graphs_model"]').append('<option value="'+model.name+'">'+ parent + '/' + model.name+'</option>');
        $('#frm-disease').find('select[name="disease_model"]').append('<option value="'+model.name+'">'+ parent + '/' + model.name+'</option>');
        $('#frm-infector').find('select[name="infector_model"]').append('<option value="'+model.name+'">'+ parent + '/' + model.name+'</option>');
    }    
    $('#frm-models').find('input').attr('value', '');
}

// Core function -- assembles all forms on a page into a JSON object,
// including the models and graph data.
// Additionally, it cleans the data up and determines relationships
function buildScenario() {

     // Make sure the user created at least one model
     if (!TMP_MODELS) {
         setStatus("No model created. Aborting.");
         return;
     }
    // display load screen
    LOADER.load();
    setStatus('Serializing and Generating JSON...');
    

    // Assemble all data and serialize all forms:

    
    var data = {};
    
    var infector = $('#frm-infector').serializeObject();
    
    var inform = $('#frm-infector');
    infector['location'] = inform.find('select[name="loc3"]').val() && (inform.find('select[name="loc3"]').val().length > 1) ? inform.find('select[name="loc3"]').val() : null || inform.find('select[name="loc2"]').val() && (inform.find('select[name="loc2"]').val().length > 1) ? inform.find('select[name="loc2"]').val() : null || inform.find('select[name="loc1"]').val() && (inform.find('select[name="loc1"]').val().length > 1) ? inform.find('select[name="loc1"]').val() : null;
    if (!infector['location']) {
        infector['location'] = infector['manual-location']
    }
    delete infector['loc1'];
    delete infector['loc2'];
    delete infector['loc3'];
    delete infector['manual-location'];
    
    var disease  = $('#frm-disease').serializeObject()
    
    if (disease['disease_model'] !== '-') {
        insertDiseaseIntoModel([TMP_MODELS], disease['disease_model'], disease);
    }
    if (infector['infector_model'] !== '-') {
        insertInfectorIntoModel([TMP_MODELS], infector['infector_model'], infector);
    }
    
    data['project_name'] = ""+Date.now();
    // Grab the form data
    data['scenario'] = $('#frm-scenario').serializeObject();
    data['disease'] = disease;
    data['infector'] = infector;
    data['models'] = TMP_MODELS;
    data['sequencer'] = $('#frm-sequencer').serializeObject();
    
    // Now do the specifics--linking names to objects
    
    data['scenario']['model'] = data['models']['name']+'.model';
    data['scenario']['sequencer'] = data['sequencer']['name']+'.sequencer';
    data['scenario']['infector'] = data['infector']['name']+'.standard';
    
    // Graphs!
    data['graphs'] = GLOBAL_GRAPHS || [];
    
    // This makes it easier on the backend, specifically storing info in the database
    data['country'] = COUNTRY_SELECTED;
    
    
    setStatus('Sending object to server...');

    // Object is now ready--transferring to the server
    $.ajax({
        type: "POST",
        url: "backend/create_scenario.php",
        dataType: "json",
        data: { data: data },
        complete: function() {
            setStatus('Completed request');
            NAV.show('load');
        }
    });
}