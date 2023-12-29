$(document).ready(function(){
    $("#studentForm").on('submit', function(event){
        event.preventDefault();

        var formData = {
            studentname: $("#studentName").val(),
            studentId: $("#studentID").val(),
            sem: $("#sem").val(),
            section: $("#section").val(),
            year: $("#year").val(),
            amount: $("#amount").val(),
            date: $("#date").val(),
            office_in_charge: $("#office_in_charge").val(),
            description: $("#description").val()
        };

        $.ajax({
            url: 'endpoint.php/postAdding',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response){
                alert('Data sent successfully');
                
            },
            error: function(error){
                console.log(error);
            }
        });
        location.reload();
    });
    
});

function refreshPage() {
    location.reload(true); // true forces a reload from the server, you can use false to reload from cache
    event.preventDefault();
  }