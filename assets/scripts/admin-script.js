jQuery(document).ready(function() {

  function calcTitle() {
    var length = jQuery('#sseo-title').val().length;
    if(length > 45 && length <= 60) {
      jQuery('#sseo-title-character-info').css('color', '#27ae60');
    } else if(length <= 45) {
      jQuery('#sseo-title-character-info').css('color', '#f39c12');
    } else {
      jQuery('#sseo-title-character-info').css('color', '#e74c3c');
    }
    jQuery('#sseo-title-character-info').html(length + '/60');
  }

  function calcMetadescription() {
    var length = jQuery('#sseo-metadescription').val().length;
    if(length > 125 && length <= 155) {
      jQuery('#sseo-metadescription-character-info').css('color', '#27ae60');
    } else if(length <= 125 && length > 0) {
      jQuery('#sseo-metadescription-character-info').css('color', '#f39c12');
    } else {
      jQuery('#sseo-metadescription-character-info').css('color', '#e74c3c');
    }
    jQuery('#sseo-metadescription-character-info').html(length + '/155');
  }

  calcTitle();
  calcMetadescription();

  jQuery('#sseo-title').keyup(function() {
    if( !jQuery(this).val() ) {
      jQuery('#sseo-preview-title').html(jQuery('#sseo-title-default').val());
    } else {
      jQuery('#sseo-preview-title').html(jQuery('#sseo-title').val());
      calcTitle();
    };
  });

  jQuery('#sseo-metadescription').keyup(function() {
    if( !jQuery(this).val() ) {
    } else {
      jQuery('#sseo-preview-metadescription').html(jQuery('#sseo-metadescription').val());
      calcMetadescription();
    };
  });

});
