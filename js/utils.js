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
                             $("#accordion").accordion();
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
