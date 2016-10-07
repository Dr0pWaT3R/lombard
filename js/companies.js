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
    //alert($(this).attr('value'));
    compID = $(this).attr('value');

    $.ajax({
        url : "getdata.php",
        type: "POST",
        data : {table: section, id: compID},
        success: function(data)
        {
            setCompany(data);
            //alert(data);
            $("#btn_upExpirD").trigger("click");
        }
    });

});

$('#up_compExpiredAt_form').submit(function() {

    var data = {};
    data['table'] = "company";
    data['id'] = compID;
    data['createdAt'] = document.getElementById('newCreatedAt').value;
    var e = document.getElementById("expire");
    data['expiredAt'] = e.options[e.selectedIndex].value;
    //alert(data.toSource());

    $.ajax({
        url : "update.php",
        type: "POST",
        data : data,
        success: function(data, textStatus, jqXHR)
        {
            var result = $.parseJSON(data);

            if (result.success) 
                alert("Амжилттай"); 
            else
                alert("Өөрчлөлт байхгүй"); 

            console.log(result.success);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        }
    });
    return false;
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

function setCompany(json) {

    document.getElementById('createdAt').innerHTML = json[0].createdAt;
    document.getElementById('expiredAt').innerHTML = json[0].expireDate;
    $('.modal-title').text(json[0].compName);
    if(json[0].expiredAt == false) {
        //alert("Хугацаа дуусаагүй байна");
        $('input[name=newCreatedAt]').val(json[0].expireDate);
    }else {
        $('input[name=newCreatedAt]').val(json[0].todayDate);
    }

}
