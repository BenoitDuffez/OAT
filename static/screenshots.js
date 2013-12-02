/**
 * Created by bicou on 02/12/2013.
 */

/**
 * The list of files that are successfully uploaded by the ajax script
 * @type {Array}
 */
var uploadedFiles = [];

/**
 * Called when the user has uploaded some screenshots and affects them to a context
 */
function addScreenshots() {
    var ctx = $('#screenshot_context').val();
    $.ajax({
        type: "POST",
        url: oatPath + "/ajax.php?action=addScreenshots",
        data: JSON.stringify({ context_id: ctx, screenshots: uploadedFiles }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) {
            $('#screenshot_files').empty();
            $('#screenshot_upload_button').attr('disabled', 'disabled');
            if (data.status == 'KO') {
                alert("Couldn't save screenshot: " + data.reason);
            }
            refreshScreenshots();
        },
        failure: function (errMsg) {
            alert(errMsg);
        }
    });
}

/**
 * Called to fill the UI
 */
function refreshScreenshots() {
    $.getJSON(oatPath + "/ajax.php?action=getScreenshots", null, function (result) {
        console.log(result);
    });
}

/**
 * File upload manager
 */
/*global window, $ */
$(function () {
    'use strict';
    $('#fileupload').fileupload({
        url: oatPath + '/upload/',
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                /*
                 file contains:
                 deleteType: "DELETE"
                 deleteUrl: "http://oat.bicou.net/upload/?file=IMG_27112013_175042%20%282%29.png"
                 name: "IMG_27112013_175042 (2).png"
                 size: 23931
                 thumbnailUrl: "http://oat.bicou.net/upload/files/thumbnail/IMG_27112013_175042%20%282%29.png"
                 type: "image/png"
                 url: "http://oat.bicou.net/upload/files/IMG_27112013_175042%20%282%29.png"
                 */
                if (file.error != undefined) {
                    $('#screenshot_files').append('Error: unable to upload file');
                } else {
                    $('#screenshot_files').append('<img class="screenshot" src="' + file.thumbnailUrl + '" />');
                    window.uploadedFiles.push(file);
                    $('#screenshot_upload_button').removeAttr('disabled');
                    $('#progress').find('.progress-bar-success').css('width', '0%');
                }
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress').find('.progress-bar-success').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});

/**
 * Refresh screenshots on load
 */
$(document).ready(function () {
    refreshScreenshots();
});