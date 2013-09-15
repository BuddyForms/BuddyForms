jQuery(document).ready(function (){
	var config = {
      '.chosen'     : {
      	width:"50%",
      	no_results_text:'No results match',
      	placeholder_text_multiple:'Select multiple Options',
		placeholder_text_single:'Select an Option',
		search_contains: true,
		allow_single_deselect: true,
      }
   };
   
   for (var selector in config) {
      jQuery(selector).chosen(config[selector]);
    }
});      



  