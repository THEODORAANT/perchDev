Perch.Apps.Podcasts = function() {
	
	var init = function() {
		initAudioMetaData()
	};

	var initAudioMetaData = function() {
		var input = $('#episodeFile');
		if (input.length && input.val()) {

			var input_duration = $('#episodeDuration');
			var input_filesize = $('#episodeFileSize');

			if (input_duration.val()=='' || input_filesize.val()=='') {

				add_spinner(input_duration);
				add_spinner(input_filesize);

				soundManager.setup({
					url: Perch.path+'/addons/apps/perch_podcasts/soundmanager2/swf/',
					onready: function() {
						var foo = soundManager.createSound({
							id: 'metadataSound',
							url: input.val(),
							autoLoad: true,
							autoPlay: false,
							onload: function() {
								console.log(this);
								var bytesTotal = this.bytesTotal;
								input_filesize.val(bytesTotal);
								input_filesize.data('spinner').remove();

								var totalSec = parseInt(this.duration / 1000);
								var hours = parseInt( totalSec / 3600 ) % 24;
								var minutes = parseInt( totalSec / 60 ) % 60;
								var seconds = totalSec % 60;
								var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
							
								input_duration.val(result);
								input_duration.data('spinner').remove();

								$.ajax('', {
									'data' : {
										'dur': totalSec,
										'bytes': bytesTotal,
									}
								});
							},
							volume: 0
						});
					}
				});
			};
		}
	}

	var add_spinner = function(el) {
		var spinner = $('<img class="spin" src="'+Perch.path+'/addons/apps/perch_podcasts/img/spin.gif" />');
		spinner.insertAfter(el);
		el.data('spinner', spinner);
	}
	
	return {
		init: init
	};
}();
