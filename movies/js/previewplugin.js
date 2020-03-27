(function(OCA) {

	OCA.FilesPdfViewer = OCA.FilesPdfViewer || {};

	/**
	 * @namespace OCA.FilesPdfViewer.PreviewPlugin
	 */
	OCA.FilesPdfViewer.PreviewPlugin = {

		/**
		 * @param fileList
		 */
		attach: function(fileList) {
			this._extendFileActions(fileList.fileActions);
		},

		/**
		 * @param fileActions
		 * @private
		 */
		_extendFileActions: function(fileActions) {
			var self = this;
			if (isSecureViewerAvailable()) {
				return;
			}
			fileActions.registerAction({
				name: 'view',
				displayName: 'Favorite',
				mime: 'video/mp4',
				permissions: OC.PERMISSION_READ,
				actionHandler: function(fileName, context) {
                    var movieUrl = OC.generateUrl(`/apps/movies/api/v1/movie/{path}`, { path: fileName })

                    debugger;
                    $.getJSON(movieUrl, function(emp){
                        debugger;
                        if(typeof(emp['movie'])!="undefined"){
                            window.location.href=OC.generateUrl('/apps/movies/watch/{fileid}', { fileid: emp['fileid'] });
                        }
                    });
				}
			});
			fileActions.setDefault('video/mp4', 'view');
		}
	};

})(OCA);

OC.Plugins.register('OCA.Files.FileList', OCA.FilesPdfViewer.PreviewPlugin);
