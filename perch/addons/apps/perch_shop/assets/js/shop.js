Perch.Apps.Shop = function() {

	var init = function() {
		initSkuHelpers();
	};

	var initSkuHelpers = function() {
		var form = $('form');
		if (form.length) {
			form.on('blur', 'input[data-sku]', function(e) {
				var field         = $(e.target);
				var attrs         = field.attr('data-sku').split(',');
				var this_id       = attrs[0];
				var target        = $('#'+field.attr('id').replace(attrs[0], attrs[1]));
				if (target.val()!= '') return;
				var id_field      = $('#'+field.attr('id').replace(attrs[0], attrs[2]));
				var option_id     = id_field.val();
				var other_codes   = $('[data-code]');
				var existing      = [];
				other_codes.each(function(i, o){
					var self = $(o);
					if (self.val()) existing.push(self.val());
				});

				$.get(Perch.path+'/addons/apps/perch_shop/async/option_sku.php', {
					value: field.val(),
					id: option_id,
					opts: existing.join(','),
					}, function(result){
						if (result) {
							target.val(result);
						}
					});
			});
		}
	}

	return {
		init: init
	};
}();
