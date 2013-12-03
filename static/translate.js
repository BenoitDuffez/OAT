/**
 * Created by bicou on 02/12/2013.
 */

/**
 * Called when the user clicks a string from the list
 * @param name The string name
 * @param lang The target language
 */
/*global $, window */
function setCurrentString(name, lang) {
    window.currentStringName = name;
    window.currentStringLang = lang;

    $('#list_strings').find('ul li').each(function (i, e) {
        $(e).removeClass('current').find('a.button').each(function (i, e) {
            $(e).removeClass('active');
        });
    });
    var current = $('li#' + window.currentStringName);
    current.addClass('current');
    current.find('a.button').each(function (i, e) {
        $(e).addClass('active');
    });
    $('#main_container').animate({height: '98%'}, 150);
    $('#topForm').animate({opacity: 1}, 1000);
    $('#context').animate({opacity: 1}, 1000);

    $.getJSON(oatPath + "/ajax.php?action=getString&name=" + name + "&lang=" + lang, null, function (data) {
        $('#sourcetext').val(data.source.text);
        $('#translatedtext').val(data.destination.text).focus();
    });
    var scr = $('#screenshots');
    $.getJSON(oatPath + "/ajax.php?action=getScreenshots&name=" + name, null, function (data) {
        prevCid = -1;
        scr.empty();
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
 * Called when Ctrl+Enter or save string
 */
function saveString() {
    var txt = $('#translatedtext').val();
    $.ajax({
        type: "POST",
        url: oatPath + "/ajax.php?action=addString",
        data: JSON.stringify({ name: window.currentStringName, lang: window.currentStringLang, text: txt }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) {
            if (data.status == 'KO') {
                alert("Couldn't save string: " + data.reason);
            } else {
                $('li#' + window.currentStringName).removeClass('unset').addClass('set').prev();
                selectNextString();
            }
        },
        failure: function (errMsg) {
            alert(errMsg);
        }
    });
}

function selectNextString() {
    var nextString = $('li#' + window.currentStringName).next().attr('id');
    setCurrentString(nextString, window.currentStringLang);
}

function selectPrevString() {
    var prevString = $('li#' + window.currentStringName).prev().attr('id');
    setCurrentString(prevString, window.currentStringLang);
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
        // Alt+Up: previous string
        if (e.keyCode == 38 && event.altKey) {
            selectPrevString();
        }
        // Alt+Down: next string
        if (e.keyCode == 40 && event.altKey) {
            selectNextString();
        }
        // Ctrl+Enter: validate translation & go to next string
        if ((e.keyCode == 10 || e.keyCode == 13) && event.ctrlKey) {
            saveString();
            e.preventDefault();
        }
    });
});
