/**
 * Created by bicou on 03/12/2013.
 */

/*global window, $*/
function exportStringsToFile(filename) {
    window.location.href = oatPath + '/xmldownload.php?filename=' + $('#filename').val() + '&lang=' + $('#lang').val();
}
