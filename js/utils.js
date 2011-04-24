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
                             $("#accordion").accordion({ autoHeight: false });
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
	             $.ajax({
                      url: "partials/_load-scenario.html",
                      timeout: 10000,
                      success: function(data){
                          page_load.find("> div").html(data);
                          page_load.show();
                      },
                      complete: function() {
                          LOADER.unload();
                      }
                   });
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
	         if (which_page !== 'create' && which_page !== 'load') {
	             setTimeout(function() { LOADER.unload(); }, 800);
	         }
        
	     }
	}());
}

/**
 * Creating objects
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

function setStatus(status) {
    $('#status').html(status);
}
var TMP_MODELS;
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
var generateModel = function() {
    var model = $('#frm-models').serializeObject();
    var first = false;
    if (!TMP_MODELS) {
        TMP_MODELS = model;
        delete TMP_MODELS.parent;
        TMP_MODELS.models = [];
        first = true;
    } else {
        var parent = model.parent;
        delete model.parent;
        model.models = [];
        
        if (TMP_MODELS.name === parent) {
            TMP_MODELS.models.push(model);
        } else {
            insertIntoModel(TMP_MODELS.models, parent, model);
        }
    }
    if (first) {
        $('#frm-models').find('select[name="parent"]').html('<option value="'+model.name+'">'+model.name+'</option>');
    } else {
        $('#frm-models').find('select[name="parent"]').append('<option value="'+model.name+'">'+ parent + '/' + model.name+'</option>');
    }
    $('#frm-models').find('input').attr('value', '');
}
function buildScenario() {
    /**
     * TODO:
     * -----
     * Each scenario needs *ONE* top level model
     * each model has an array: models = [model2, model3, ...]
     * Serialize to a temp object first, modify as necessary THEN assign accordingly to main JSON object
     * ====================
     * Drag-n-drop graphs
     * add graph arrays to EACH model
     */
    if (!TMP_MODELS) {
        setStatus("No model created. Aborting.");s
    }
    LOADER.load();
    setStatus('Serializing and Generating JSON...');
    
    var data = {};
    data['project_name'] = 'blah';
    // Grab the form data
    data['scenario'] = $('#frm-scenario').serializeObject();
    data['disease'] = $('#frm-disease').serializeObject();
    // TODO: Figure graphs out
    //data['graphs'] = null;
    data['infector'] = $('#frm-infector').serializeObject();
    data['models'] = TMP_MODELS;
    data['sequencer'] = $('#frm-sequencer').serializeObject();
    
    // Now do the specifics--linking names to objects
    
    data['scenario']['model'] = data['models']['name']+'.model';
    data['scenario']['sequencer'] = data['sequencer']['name']+'.sequencer';
    data['scenario']['infector'] = data['infector']['name']+'.standard';
    
    // TODO: Figure out what to do with this
    data['models']['graphs'] = {};
    
    console.log(data);
    
    setStatus('Sending object to server...');
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