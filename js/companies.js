$(document).ready(function(){
    $('.dataTables-example').DataTable({
        dom: '<"html5buttons"B>lTfgitp',
    buttons: [
    { extend: 'copy'},
    {extend: 'csv'},
    {extend: 'excel', title: section},
    {extend: 'pdf', title: section},

    {extend: 'print',
        customize: function (win){
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');
            $(win.document.body).find('table')
        .addClass('compact')
        .css('font-size', 'inherit');
        }
    }
    ]

    });
});

var compID;
$('.updateBtn').on('click', function() {
    alert($(this).attr('value'));

    $("#btn_upExpirD").trigger("click");
});

$('.deleteBtn').on('click', function () {
    //alert($(this).attr('value'));
    compID = $(this).attr('value');
    bootbox.dialog({
        message: "Та энэ компани мэдээлэлийг устгах гэж байна?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
            success: {
                label: "Болих",
                className: "btn-success",
                callback: function() {
                    $('.bootbox').modal('hide');
                }
            },
            danger: {
                label: "Устгах",
                className: "btn-danger",
                callback: function() {

                    $.ajax({ url: "delete.php", 
                        type: "POST", 
                        data : { table: section, id: compID}, 
                        success: function(data) { 
                            //console.log(data); 
                            window.location.replace("index.php?companies");
                            var result = JSON.parse(data);

                            if (result.success) 
                                alert("Амжилттай"); 
                            else
                                alert("Өөрчлөлт байхгүй"); 

                            //console.log(result.success);
                        }, 
                        error: function (jqXHR, textStatus, errorThrown) {
                        } 
                    });
                }
            }
        }
    });

});
