(function () {
	var IMPROVE_ICON = 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="#333" d="M6 1l1.2 3.4L10.5 5.5 7.2 6.7 6 10.1 4.8 6.7 1.5 5.5 4.8 4.4z"/><path fill="#333" d="M12 8l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7z"/></svg>');
	var SUMMARIZE_ICON = 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><rect fill="#333" x="2" y="3" width="12" height="2"/><rect fill="#333" x="2" y="7" width="12" height="2"/><rect fill="#333" x="2" y="11" width="8" height="2"/></svg>');

	CKEDITOR.plugins.add('perchai', {
		init: function (editor) {
			var endpoint = (window.Perch && window.Perch.path ? window.Perch.path : '') +
				'/addons/plugins/editors/ckeditor/perch/ai/ai.php';

			var getSelectionHtml = function () {
				var sel = editor.getSelection();
				if (!sel) return '';
				var ranges = sel.getRanges();
				if (!ranges || !ranges.length || ranges[0].collapsed) return '';
				var wrap = new CKEDITOR.dom.element('div');
				wrap.append(ranges[0].cloneContents());
				return wrap.getHtml();
			};

			var run = function (action, label) {
				var selectionHtml = getSelectionHtml();
				var replaceAll = !selectionHtml;
				var payloadHtml = selectionHtml || editor.getData();

				if (!payloadHtml || !payloadHtml.replace(/<[^>]+>/g, '').trim()) {
					editor.showNotification('Nothing to ' + label.toLowerCase() + '.', 'warning');
					return;
				}

				var notice = editor.showNotification(label + '…', 'progress', 0);

				var xhr = new XMLHttpRequest();
				xhr.open('POST', endpoint, true);
				xhr.setRequestHeader('Content-Type', 'application/json');
				xhr.onreadystatechange = function () {
					if (xhr.readyState !== 4) return;
					notice.hide();
					var resp;
					try { resp = JSON.parse(xhr.responseText); } catch (e) { resp = null; }

					if (xhr.status !== 200 || !resp || !resp.ok) {
						var msg = (resp && resp.error) ? resp.error : ('HTTP ' + xhr.status);
						editor.showNotification('AI error: ' + msg, 'warning');
						return;
					}

					if (replaceAll) {
						editor.setData(resp.html);
					} else {
						editor.insertHtml(resp.html);
					}
					editor.showNotification(label + ' applied.', 'success');
				};
				xhr.send(JSON.stringify({ action: action, html: payloadHtml }));
			};

			editor.addCommand('perchaiImprove', { exec: function () { run('improve', 'Improving writing'); } });
			editor.addCommand('perchaiSummarize', { exec: function () { run('summarize', 'Summarizing'); } });

			editor.ui.addButton('PerchAIImprove', {
				label: 'Improve writing (AI)',
				command: 'perchaiImprove',
				toolbar: 'insert,100',
				icon: IMPROVE_ICON
			});

			editor.ui.addButton('PerchAISummarize', {
				label: 'Summarize (AI)',
				command: 'perchaiSummarize',
				toolbar: 'insert,101',
				icon: SUMMARIZE_ICON
			});
		}
	});
})();
