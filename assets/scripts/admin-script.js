jQuery(document).ready(function() {

  // GENERAL FUNCTIONS
  //-----------------------------------------------------------------------

  // Displays the current and the max length
  function displayLength(inputfield, outputelement, okaylength, maxlength) {
    var length = jQuery(inputfield).val().length;

    // Highlight length info in color for feedback
    if (length > okaylength && length <= maxlength) {
      jQuery(outputelement).css('color', '#27ae60');
    } else if (length <= okaylength && length > 0) {
      jQuery(outputelement).css('color', '#f39c12');
    } else {
      jQuery(outputelement).css('color', '#e74c3c');
    }

    // Write current length to output
    jQuery(outputelement).html(length + '/' + maxlength);
  }

  // SETTINGS PAGE
  //-----------------------------------------------------------------------

  // TITLE PATTERN
  if (jQuery('#sseo_title_pattern').length) {
    displayLength('#sseo_title_pattern', '#sseo_title_pattern_info', 45, 60);
    jQuery('#sseo_title_pattern').on('input', function() {
      displayLength('#sseo_title_pattern', '#sseo_title_pattern_info', 45, 60);
    });
  }

  // DEFAULT METADESCRIPTION
  if (jQuery('#sseo_default_metadescription').length) {
    displayLength('#sseo_default_metadescription', '#sseo_default_metadescription_info', 125, 155);
    jQuery('#sseo_default_metadescription').on('input', function() {
      displayLength('#sseo_default_metadescription', '#sseo_default_metadescription_info', 125, 155);
    });
  }


  // METABOX
  //-----------------------------------------------------------------------

  // TITLE INPUT
  if (jQuery('#sseo-title').length) {

    displayLength('#sseo-title', '#sseo-title-info', 45, 60);

    jQuery('#sseo-title').on('input', function() {
      if( !jQuery(this).val() ) {
        jQuery('#sseo-preview-title').html(jQuery('#sseo-title-default').val());
      } else {
        jQuery('#sseo-preview-title').html(jQuery('#sseo-title').val());
      };

      displayLength('#sseo-title', '#sseo-title-info', 45, 60);
    });
  }

  // METADESCRIPTION INPUT
  if (jQuery('#sseo-metadescription').length) {

    displayLength('#sseo-metadescription', '#sseo-metadescription-info', 125, 155);

    jQuery('#sseo-metadescription').on('input', function() {
      if( !jQuery(this).val() ) {
        jQuery('#sseo-preview-metadescription').html(jQuery('#sseo-metadescription-default').val());
      } else {
        jQuery('#sseo-preview-metadescription').html(jQuery('#sseo-metadescription').val());
      };

      displayLength('#sseo-metadescription', '#sseo-metadescription-info', 125, 155);
    });
  }

});
