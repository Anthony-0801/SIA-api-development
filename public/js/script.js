$("#display").click(function(){

    $.post("http://localhost/api/public/printName",
    function(data, status){
    var json=JSON.parse(data);
    var row="";
    for(var i=0;i<json.data.length;i++){
    
    row=row+"<tr><td>"+json.data[i].lname+"</td><td>"+json.data[i].fname+"</td></tr>";
    
    }
    $("#data").get(0).innerHTML=row;
    });
    });