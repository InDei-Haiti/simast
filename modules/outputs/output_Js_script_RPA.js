/**
 * Created by rulxphilome.alexis on 10/30/2017.
 */
var entetes = ["Nom","Type","Item Type","Description"], i;
var selectedRows = {
    "slct_1" : "<tr><td colspan='\"2\"'></td></tr>"
}
$(document).ready(function(){
    for( i =0; i< entetes.length; i++){
        $("#select_field").append("<option value=\""+ i +"\">" + entetes[i] +
            "</option>");
    }

});


function setTableFilter_output(){
    // console.log(trRows);
    var filterstab = document.getElementById('filterstab');
    var val = document.getElementById('select_field').value;
    alert("slct_"+val);
    // console.log(val);
    if(val!="---" && selectedRows["slct_"+val])
    //filterstab.innerHTML += trRows[val];
        $('#filterstab').append(selectedRows["slct"+val]);
    $j(".classflddate").datepick({dateFormat: "yyyy-mm-dd"});
}