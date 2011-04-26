README

flags.json : arr[ISO] => country-flag.gif

ISO-3166-1.json : arr[ISO] => "country long name" 

selectoptions.json : look at or ask me...

graphs.json : arr[ISO][i]["path"]  => path to the location of the i-th graph of country ISO
	      arr[ISO][i]["file"] => short path/file information for the user menus for the i-th graph of country ISO

models.json  see graphs.json

full_ISO_3166.json: 	//NOTE this file is 1.2 MB.  If you only need ISO->country long name data, perhaps ISO-3166-1 (5Kb) is better for your needs.
			
			arr[ISO][0] => "country (level 0) long name"
			
			arr[ISO][1][i]["code"] => 'five to 7 letter region code for the ith level 1 region in this country]'
			arr[ISO][1][i]["name"] => 'long name of i-th level 1 region'
			
			arr[ISO][2][i]["code"] => 'extended ISO for ith level 2 region'
			arr[ISO][2][i]["name"] => 'long name of ith level 2 region'

			arr[ISO][c][0]["lat"] => "latitude for this ISO country (level 0) center"
			arr[ISO][c][0]["long"] => "longitude for center "" at level 0" 

			//NOTE only level 0 center lat/longs are implemented
