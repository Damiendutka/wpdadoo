jQuery(document).ready(function()
{
	// Characters left for description and keywords on page load

	var charLeftDescription = 300 - jQuery("#optionDescription").val().length;
    jQuery("#nbDescriptionLeft").html(charLeftDescription);
    jQuery("#descriptionLeft").show();

    var charLeftKeywords = 1000 - jQuery("#optionKeywords").val().length;
    jQuery("#nbKeywordsLeft").html(charLeftKeywords);
    jQuery("#keywordsLeft").show();

    // Characters left for description and keywords on each textarea input change

	jQuery("#optionDescription").on("change keyup paste", function() {
	    var charLeft = 300 - jQuery(this).val().length;

	    jQuery("#nbDescriptionLeft").html(charLeft);
	    jQuery("#descriptionLeft").show();
	});

	jQuery("#optionKeywords").on("change keyup paste", function() {
	    var charLeft = 1000 - jQuery(this).val().length;

	    jQuery("#nbKeywordsLeft").html(charLeft);
	    jQuery("#keywordsLeft").show();
	});
});