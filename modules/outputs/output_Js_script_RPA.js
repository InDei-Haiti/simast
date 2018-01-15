

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
                        $(this).css("display","none");
                        // $(this).removeAttr("style");
                    }
                }
            }else{
                // $(this).removeAttr("style");
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

function cacheReport(id_report,cv,chx){
    if(chx == 0){
        if(confirm("You want desactive this report ?")){
            // var $qr=$j("#qsr_"+cv);
            // var data='mode=query&imode=del&stype='+$qr.find("td:eq(2)").text()+
            //     '&sid='+$qr.find(".qeditor").attr("data-id");
            $j.ajax({
                url: "?m=outputs&a=reports&mode=desactive_report&suppressHeaders=1&idntf="+id_report+"&chx="+chx,
                type: 'get',
                success: function(data){
                    if(data = 'ok'){
                        // alert("Reussi");
                        // $("[id^=qsr_][data-id = "+id_report+"]").html();
                        // console.log($("#qsr_"+cv).html());
                        $("#qsr_"+cv).fadeOut('slow',function(){
                            $("#qsr_"+cv).remove();
                        });
                    }
                }
            });
        }
    }else if(chx == 1){
        if(confirm("You want desactive this item ?")){
            $j.ajax({
                url: "?m=outputs&a=reports&mode=desactive_report&suppressHeaders=1&idntf="+id_report+"&chx="+chx,
                type: 'get',
                success: function(data){
                    if(data = 'ok'){
                        // alert("Reussi");
                        // $("[id^=qsr_][data-id = "+id_report+"]").html();
                        // console.log($("#qsr_"+cv).html());
                        $("#qsr_"+cv).fadeOut('slow',function(){
                            $("#qsr_"+cv).remove();
                        });
                    }
                }
            });
        }
    }
}


// Unhide Element Cached

$(document).ready(function(){
    $("#showCachedElms").click(function(ev){
        ev.preventDefault();
        $j.ajax({
            url: "?m=outputs&a=reports&mode=getHidedElmts&cached=0&suppressHeaders=1",
            type: 'get',
            success: function(data){
              $("#unn tbody").empty();
              data = JSON.parse(data);
              console.log(data.reports);
                $("#unn").append("<tr><td>Nom</td><td>Type</td><td>Decacher</td></tr>");
              for(var i = 0; i < data.reports.length; i++){
                  $("#unn").append("<tr><td>"+data.reports[i].title+"</td><td>Report</td><td><span onclick='unHideElmts("+data.reports[i].id+",0)'>Afficher</span></td></tr>");
              }
            }
        });

        $('input[type=radio][name=choix]').change(function() {
            if (this.value == 'items') {
              $j.ajax({
                  url: "?m=outputs&a=reports&mode=getHidedElmts&cached=0&suppressHeaders=1",
                  type: 'get',
                  success: function(data){
                    $("#unn tbody").empty();
                    data = JSON.parse(data);
                    console.log(data.reports);
                      $("#unn").append("<tr><td>Nom</td><td>Type</td><td>Decacher</td></tr>");
                    for(var i = 0; i < data.items.length; i++){
                        $("#unn").append("<tr><td>"+data.items[i].title+"</td><td>"+data.items[i].itype+"</td><td><span onclick='unHideElmts("+data.reports[i].id+",1)'>Afficher</span></td></tr>");
                    }
                  }
              });
            }
            else if (this.value == 'reps') {
              $j.ajax({
                  url: "?m=outputs&a=reports&mode=getHidedElmts&cached=0&suppressHeaders=1",
                  type: 'get',
                  success: function(data){
                    $("#unn tbody").empty();
                    data = JSON.parse(data);
                    console.log(data.items);
                      $("#unn").append("<tr><td>Nom</td><td>Type</td><td>Decacher</td></tr>");
                    for(var i = 0; i < data.reports.length; i++){
                        $("#unn").append("<tr><td>"+data.reports[i].title+"</td><td>Report</td><td><span onclick='unHideElmts("+data.items[i].id+",0)'>Afficher</span></td></tr>");
                    }
                  }
              });
            }
        });
        var getModal = $("#unHideSelector");
        getModal.css("display","block");
        $("#closeUnHideSelector").click(function(){
          getModal.css("display","none");
        });
    });
});

function unHideElmts(id,chx){
    if(chx == 0){
      $j.ajax({
          url: "?m=outputs&a=reports&mode=getHidedElmts&cached=1&suppressHeaders=1&chx="+chx+"&id="+id,
          type: 'get',
          success: function(data){
              if(data == "ok"){
                  location.reload();
              }
          }
      });
    }else{
      $j.ajax({
          url: "?m=outputs&a=reports&mode=getHidedElmts&cached=1&suppressHeaders=1&chx="+chx+"&id="+id,
          type: 'get',
          success: function(data){
            if(data == "ok"){
                location.reload();
            }
          }

      });
    }
}
