(function() {
	tinymce.PluginManager.add('zoomph_tc_button', function( editor, url ) {
		editor.addButton( 'zoomph_tc_button', {
			text: 'Zoomph Visual',
			icon: false,
			onclick: function() {
				console.log(editor.windowManager.open( {
					title: 'Zoomph Visual',
					body: [{
						type: 'textbox',
						name: 'visualId',
						label: 'Visual Id'
					}, {
						type: 'checkbox',
						name: 'autoWidth',
						label: 'Width',
						text: 'Auto',
						checked: true,
						onclick: function ( e ) {
							var widthControl = e.control.rootControl.find('.zoomphwidth');
							widthControl.value('');
							widthControl.disabled(this.value());
						}
					}, {
						type: 'textbox',
						name: 'width',
						disabled: true,
						label: ' ',
						classes: 'zoomphwidth'
					}, {
						type: 'textbox',
						name: 'height',
						label: 'Height'
					}],
					onsubmit: function( e ) {
						var attrs = [];
						
						attrs.push('id=' + e.data.visualId.trim());
						
						if (e.data.width.trim() !== '')
							attrs.push('width=' + parseInt(e.data.width.trim()));
						if (e.data.height.trim() !== '')
							attrs.push('height=' + parseInt(e.data.height.trim()));
						
						editor.insertContent( '[zoomph ' + attrs.join(' ') + ']');
					}
				}));
			}
		});
	});
}());