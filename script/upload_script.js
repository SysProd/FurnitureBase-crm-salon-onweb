
var PhotosResult = "";
var Count = 0;
var UploadedFiles = 0;
var name_files = '';
function photos_fileDialogComplete(numFilesSelected, numFilesQueued) {
    try {
        if (numFilesQueued > 0) {
            PhotosResult = numFilesQueued == '1' ? ' file' : ' images';
            PhotosResult = numFilesQueued + PhotosResult + " attached";
            Count = parseInt(numFilesQueued);
            $('#AddPhotos').val('Uploading...');
            $('#submitStatus')
                .attr('disabled', 'disabled')
                .addClass('disabled');
            this.startUpload();
        }
    } catch (ex) {
    }
}

function photos_uploadProgress(file, bytesLoaded) {
    try {
        var pw = 115;
        var w = Math.ceil(pw * (UploadedFiles / Count + (bytesLoaded / (file.size * Count))));
        $('#Progress').stop().animate({ width: w });
    } catch (ex) {
    }
}
function photos_uploadSuccess(file, serverData) {
    try {
        UploadedFiles++;
    } catch (ex) {

    }
	name_files = serverData;
}

function photos_uploadComplete(file) {
if(name_files != 'error format file' && name_files != 'error file size > 1 MB'){
    try {
        if (this.getStats().files_queued > 0) {
            this.startUpload();
        } else {
            $('#dialog-message h3').text('Загруженный файл со списком товаров:');
            $('#UploadPhotos').hide();
            $('#Buttons').prepend(name_files);
			$('.ui-button').show();
			$('span.ui-button-text').attr('id', 'excel_1');
			$('.ui-dialog-titlebar-close').hide();
        }
    } catch (ex) {
    }	}else{
	$('#AddPhotos').val('Загрузка');
	$('#Buttons').remove();
	alert('Возникла непредвиденная ошибка');
	exit;
	}
	}
function photos_fileQueueError(file, errorCode, message) {
    try {
        switch (errorCode) {
            case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                alert('Слишком много. Максимум - один файл.');
                break;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                break;
        }
    } catch (ex) {
    }

}

function swfuploadLoaded() {
    $('#Buttons object').hover(
        function() {
            $(this).next().addClass('hover');
        },
        function() {
            $(this).next().removeClass('hover');
        });

}
var ASPSESSID = "";
var swfuPhotos;
function BindSWFUpload() {
    var swfuPhotosSettings = {
        file_dialog_complete_handler: photos_fileDialogComplete,
        upload_progress_handler: photos_uploadProgress,
        upload_success_handler: photos_uploadSuccess,
        upload_complete_handler: photos_uploadComplete,
        swfupload_loaded_handler: swfuploadLoaded,
        file_queue_error_handler: photos_fileQueueError,

        file_size_limit: "2 MB",
        file_types: "*.ods;*.xls",
        file_types_description: "XLS",
        file_upload_limit: "1",
        button_placeholder_id: "fAddPhotos",
		debug: false
    }

    var defaultSettings = {
        flash_url: "../swf/upload_swf.swf",
        upload_url: "../php_file/upload.php",
        post_params: {
            "ASPSESSID": ASPSESSID
        },

        button_width: 115,
        button_height: 32,
        button_image_url: "../img/upload/white50.png",

        button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
        button_cursor: SWFUpload.CURSOR.HAND
    }

    swfuPhotos = new SWFUpload($.extend(swfuPhotosSettings, defaultSettings));
}


