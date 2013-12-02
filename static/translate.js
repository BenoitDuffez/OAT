/**
 * Created by bicou on 02/12/2013.
 */

/**
 * Called when the user clicks a string from the list
 * @param name The string name
 * @param lang The target language
 */
/*global $ */
function setCurrentString(name, lang) {
    $.getJSON(oatPath + "/ajax.php?action=getString&name=" + name + "&lang=" + lang, null, function (data) {
        $('#topForm').css("visibility", "visible");
        $('#context').css("visibility", "visible");
        $('#sourcetext').val(data.source.text);
        $('#translatedtext').val(data.destination.text).focus();
    });
    var scr = $('#screenshots');
    scr.empty();
    $.getJSON(oatPath + "/ajax.php?action=getScreenshots&name=" + name, null, function (data) {
        prevCid = -1;
        if (data.length > 0) {
            scr.append('<p>To help with the translation, here\'s the string context:</p>')
            $.each(data, function (i, screen) {
                if (prevCid != screen.context_id) {
                    if (prevCid > 0) {
                        scr.append('</div>');
                    }
                    scr.append('<div class="context"><h3>' + screen.context_name);
                }
                prevCid = screen.context_id;
                scr.append('<div class="screenshot"><img class="screenshot" src="' + oatPath + '/upload/files/' + screen.name + '" /></div>');
            });
            scr.append('</div>');
        } else {
            var help = 'There is no associated context for this string. ';
            help += 'If you want, you can <a href="' + oatPath + '/contexts/' + name + '">choose a context</a> for this string.';
            scr.append('<p>' + help + '</p>');
        }
    });
}

/**
 * Handle keyboard shortcuts on the translated text box
 */
$(document).ready(function () {
    $('#translatedtext').keydown(function (e) {
        // Alt+Right: copy from source language
        if (e.keyCode == 39 && event.altKey) {
            $('#translatedtext').val($('#sourcetext').val());
        }
//              // Ctrl+Enter: validate translation & go to next string
//				if ((e.keyCode == 10 || e.keyCode == 13) && event.ctrlKey) {
//					$('#topForm').submit();
//					e.preventDefault();
//				}
    });
});
