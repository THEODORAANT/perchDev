$(function() {

	var set_up_ckeditor = function() {

		$('.ckeditor').removeClass('ckeditor').addClass('perch-ckeditor');

		CKEDITOR.config.filebrowserUploadUrl = Perch.path + '/addons/plugins/editors/ckeditor/perch/uploader.php?filetype=file';
		CKEDITOR.config.filebrowserImageUploadUrl = Perch.path + '/addons/plugins/editors/ckeditor/perch/uploader.php?filetype=image';
		CKEDITOR.on('instanceReady',function(e){
			var editor = e.editor;	
			var textarea = $('#'+editor.name);
			var config = '';

			config += '&width=' 	+(textarea.attr('data-width')||'');
			config += '&height=' 	+(textarea.attr('data-height')||'');
			config += '&crop='		+(textarea.attr('data-crop')||'');
			config += '&density='	+(textarea.attr('data-density')||'');
			config += '&sharpen='	+(textarea.attr('data-sharpen')||'');
			config += '&quality='	+(textarea.attr('data-quality')||'');
			config += '&bucket='	+(textarea.attr('data-bucket')||'default');

			editor.config.filebrowserImageUploadUrl += config;
			editor.config.filebrowserUploadUrl 		+= '&bucket=' +(textarea.attr('data-bucket')||'default');
		});

		$('textarea.perch-ckeditor:not([data-init])').each(function(i,o){
			var self = $(o);
			
			if (!self.parents('.spare').length) {
				self.wrap('<div class="editor-wrap ckeditor-wrap"></div>');
				CKEDITOR.replace(o);
				self.attr('data-init', true);
			};
			
		});
	};

	set_up_ckeditor();

	$(window).on('Perch_Init_Editors', function(){
		set_up_ckeditor();
	});

	$(window).on('Perch.FieldTypes.redraw', function(){
		set_up_ckeditor();
	});

});