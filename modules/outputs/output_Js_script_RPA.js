

var entetes = ["Nom","Type","Item Type","Description"], i;
var elements;
// var typeSelection = Object.keys(elements);

function buildSelection (title,optSlct,chx){
   var strLine =  '<tr>' +
    '<td><span onclick="delRowFilter(this)"style="width: 16px;height: 16px;padding: 1px;cursor: pointer;font-weight: 800;float: left;background-color: #B0B0B0;margin: 2px;text-align: center;background: url(\'/modules/wizard/images/icns.png\') no-repeat;background-position: -18px 1px;">&nbsp;&nbsp;</span></td>' +
    '<td style="padding:3px;width:20%">'+title+'</td>' +
    '<td>';
    var strSlector;
    switch (chx){
        case '1':
            strSlector = '<select name="type_equality">' +
                '<option value="=">is</option>' +
                '<option value="<>">is not</option>' +
                '</select>' +
                '</td>' +
                '<td style="padding:3px">' +
                '<select name="type_value">' +
                '<option value="---"></option>'
            ;
            for (i = 0; i < optSlct.length; i++) {
                strSlector += '<option value="'+optSlct[i]+'" >' + optSlct[i] + '</option>';
            }
            strSlector += '</select>';
            break;
        case '0':
        strSlector = '<select name="name_equality">' +
            '<option value="in">content</option>' +
            '<option value="notIn">not content</option>' +
            '</select>' +
            '</td>' +
            '<td style="padding:3px">' +
            '<input type="text" name="name_string" />'
        ;
        break;
        case '3':
            strSlector = '<select name="desc_equality">' +
                '<option value="in">content</option>' +
                '<option value="notIn">not content</option>' +
                '</select>' +
                '</td>' +
                '<td style="padding:3px">' +
                '<input type="text" name="desc_string" />'
            ;
            break;
        case '2':
            alert("En developpement");
            // strSlector = '<select name="choiceselector">' +
            //     '<option value="=">is</option>' +
            //     '<option value="<>">is not</option>' +
            //     '</select>' +
            //     '</td>' +
            //     '<td style="padding:3px">' +
            //     '<select name="optSelection">' +
            //     '<option value="---"></option>'
            // ;
            // for (i = 0; i < optSlct.length; i++) {
            //     strSlector += '<option value="09" >' + optSlct[i] + '</option>';
            // }
            // strSlector += '</select>';
            strSlector = '';strLine='';
            break;
        default :
            strSlector = '';strLine=''; break;

    }
    strLine +=strSlector +"</td></td>";

    return strLine;
}

$(document).ready(function(){
    $.get( "?m=outputs&a=reports&mode=forfilter&suppressHeaders=1", function( data ) {
        elements = JSON.parse(data);
        console.log(elements);
        console.log(Object.keys(elements));
        // console.log(buildSelection("Un Test",Object.keys(elements),1));
    });
    for( i =0; i< entetes.length; i++){
        $("#select_field").append("<option value=\""+ i +"\">" + entetes[i] +
            "</option>");
    }

});


function setTableFilter_output(){
    // console.log(trRows);
    var filterstab = document.getElementById('filterstab');
    var val = document.getElementById('select_field').value;
    var leText;
    // console.log(val);
    if(val!="")
        $("#select_field option").each(function(){
            if($(this).val() == val){
                leText = $(this).text();
            }
        });


        $('#filterstab  tbody').append(buildSelection(leText,Object.keys(elements),val));
    $j(".classflddate").datepick({dateFormat: "yyyy-mm-dd"});
}

function delRowFilter(ele){
    $span=$j(ele);
    $tr = $span.closest('tr');
    $tr.remove();
}

// Utilitaires
function toObject(arr) {
    var rv = {};
    for (var i = 0; i < arr.length; ++i)
        rv[i] = arr[i];
    return rv;
}
// Utilitaires

// Gestion soumission filtering
$("#submitButton").click(function(){
    $("table#qtable tbody").find("tr").each(function(){
        $(this).removeAttr("style");
    });
    // alert("Je suis Alexis");
    var name_equality = '',name_string='',type_equality='',type_value='',desc_equality='',desc_string='';
    choices = $("#filterform").serializeArray();
    console.log(choices);

    choices.forEach(function(vals){
        if(vals["name"] == "name_equality"){
            name_equality = vals["value"];
        }else if(vals["name"] == "name_string"){
            name_string= vals["value"];
        }else if(vals["name"] == "type_equality"){
            type_equality= vals["value"];
        }else if(vals["name"] == "type_value"){
            type_value= vals["value"];
        }else if(vals["name"] == "desc_equality"){
            desc_equality =  vals["value"];
        }else if(vals["name"] == "desc_string"){
            desc_string =  vals["value"];
        }
    });
    arr = [name_equality,name_string,type_equality,type_value,desc_equality,desc_string];
    console.log(arr);

    $("table#qtable tbody").find("tr").each(function(){
        if(name_equality && name_equality != ''){
            var sttr = $(this).find("td:eq(0)").text();
            if(sttr != ""){
                if(name_equality == 'in'){
                    if(sttr.toLowerCase().search(name_string.toLowerCase())== -1){
                        $(this).css("display","none");
                    }
                }else{
                    if(sttr.toLowerCase().search(name_string.toLowerCase())!= -1){
                        $(this).removeAttr("style");
                    }
                }
            }else{
                // $(this).removeAttr("style");
                // alert("Merde");
            }
        }else if(type_equality && type_equality!=''){
            var sttr = $(this).find("td:eq(1)").text();
            if(sttr != ""){
                // alert(type_equality);
                if(type_equality == '='){
                    if(sttr.toLowerCase().search(type_value.toLowerCase())== -1){
                        $(this).css("display","none");
                    }
                }else{
                    if(sttr.toLowerCase().search(type_value.toLowerCase())!= -1){//
                        $(this).removeAttr("style");
                    }
                }
            }else{
                // $(this).removeAttr("style");
                // alert("Merde");
            }
        }else if(desc_equality && desc_equality!=''){
            var sttr = $(this).find("td:eq(3)").text();
            if(sttr != ""){
                alert(desc_equality);
                if(desc_equality == 'in'){
                    if(sttr.toLowerCase().search(desc_string.toLowerCase())== -1){
                        $(this).css("display","none");
                    }
                }else{
                    if(sttr.toLowerCase().search(desc_string.toLowerCase())!= -1){
                        $(this).removeAttr("style");
                    }
                }
            }else{
                // $(this).removeAttr("style");
                // alert("Aucun champs ne contient de description");
            }
        }
    });

});




// Modification Sommation Table

function isInArray(value, array) {
    return array.indexOf(value) > -1;
}

Array.prototype.unique = function() {
    var a = this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};

function sommation(){
    var premier_occ = $("#tthome table thead tr:eq(1) th:eq(1)").text();
    var tab_tete = [], slected = [];
    var equality = [];
    var in_ndex = -1;
    $("#tthome table thead tr:eq(1) th").each(function(){
        // alert($(this).text());
        // var atrib = $(this).attr("rowspan");
        if($(this).index()>=1){
            if($(this).text()==='Grand Total'){
                if($(this).index()){
                    in_ndex = 0;
                }
            }
            if(in_ndex != 0){
                tab_tete.push($(this).text());
            }

        }
    });
    for(var i =0; i < tab_tete.length;i++){

        console.log(tab_tete[i]);
    }


    // Finding Duplicates
    for(var i = 0;i<tab_tete.length;i++){
        var lePush = [];
        for(var j = i+1; j < tab_tete.length;j++){
            if(tab_tete[i] == tab_tete[j]){
                lePush.push(j);
                slected.push(j);
            }
        }
        if(!isInArray(i,slected)){
            lePush.push(i);
            slected.push(i);
            equality.push({
                "nom":tab_tete[i],
                "id_commun":lePush
            });
        }else{
            continue;
        }

    }
    console.log(equality);

    $("#tthome table thead tr:eq(1)").each(function(){
        var part1_bkp = $(this).clone();
        var part2_bkp = $(this).clone();

        part1_bkp.children().each(function(){
            if($(this).index()>0){
                $(this).remove();
            }
        });

        var numm = 1+ tab_tete.length;
        part2_bkp.find("th:lt("+numm+")").remove();

        $(this).empty();
        $(this).append(part1_bkp.find("th"));

        for(var z = 0;z < equality.length;z++){
            $(this).append("<th data-ptile='"+equality[z].nom+"'>"+equality[z].nom+"</th>");
        }

        $(this).append(part2_bkp.find("th"));
    });

    $("#tthome table tbody tr").each(function(){
        var part1_bkp = $(this).clone();
        var part2_bkp = $(this).clone();


        // Sauvegarde de la premiere TD dans chaque TR
        part1_bkp.children().each(function(){
            if($(this).index()>0){
                $(this).remove();
            }
        });
        // Sauvegarde de la premiere TD dans chaque TR

        // Sauvegarde des derniers TD dans chaque TR
        var numm = 1+ tab_tete.length;
        part2_bkp.find("td:lt("+numm+")").remove();
        // Sauvegarde des derniers TD dans chaque TR
        // console.log( equality.length);
        var somme = [] ;
        for(var z = 0;z < equality.length;z++){
            console.log(equality[z].nom);
            console.log(equality[z].id_commun);
            var somme_1 = 0;
            for(var m = 0;m < equality[z].id_commun.length; m++){
                var adel = equality[z].id_commun[m]+1;
                somme_1 += parseInt($(this).find("td:eq("+adel+")").text());
            }
            somme.push(somme_1);
        }

        $(this).empty();
        $(this).append(part1_bkp.find("td"));
        for(var add = 0;add < somme.length;add++){
            $(this).append("<td class='vdata'>"+somme[add]+"</td>");
        }
        $(this).append(part2_bkp.find("td"));
        // console.log($(this).html());
        // $(this).children().each(function(){
        //     if($(this).index() == 0){
        //         console.log($(this));
        //     }
        // });
    });
}