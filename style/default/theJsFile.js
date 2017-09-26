$(document).ready(function(){
    $("#do_search").click(function(e){
        e.preventDefault();
        var words = $("#wordSearch").val();
        $("table#qtable tbody").find("tr").each(function(){
            var sttr = $(this).find("td:eq(0)").text();
            if(sttr != ""){
                if(sttr.toLowerCase().search(words.toLowerCase())== -1){
                    $(this).css("display","none");
                }
            }else{
                // $(this).removeAttr("style");
                alert("Merde");
            }

        });
        // alert("merde");
    });
});