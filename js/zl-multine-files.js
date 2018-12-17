jQuery(document).ready(function($) {
    $('#mfcf7_zl_add_file').on('click', function() {
      var zl_filecontainer = '#mfcf7_zl_multifilecontainer';
        var dname = $(zl_filecontainer).append($('#mfcf7_zl_multifilecontainer span.mfcf7-zl-multiline-sample').html());
        $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last').hide();

        $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last input').change('input',function(e) {

            var files = $(this)[0].files;
            for (var i = 0; i < files.length; i++) {
              $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last span.mfcf7-zl-multifile-name').append(files[i].name + "&nbsp;");
            }
            $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last').show();
            $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last input').hide();
            $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last .mfcf7-zl-multifile-name').show();
			$(zl_filecontainer + ' p.wpcf7-form-control-wrap:last a.mfcf7_zl_delete_file').show();
        });
		$(zl_filecontainer + ' p.wpcf7-form-control-wrap:last a.mfcf7_zl_delete_file').hide();
		var fname = $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last').find('input').trigger('click');
        $(zl_filecontainer + ' p.wpcf7-form-control-wrap:last input').hide();
        $('.mfcf7_zl_delete_file').on('click', function() {
            var get_parent = $(this).parent().remove();
        });
        document.addEventListener('wpcf7mailsent', function(event) {
            jQuery(zl_filecontainer + '>p').remove();
        });
    });
});