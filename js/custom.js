$(document).ready(function(){
    //var oTable = $('.dataTables-example').DataTable();
    $('.debitTable').DataTable( {
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
        ,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
            };

            // Total over all pages
            total = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total over this page
            pageTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 3 ).footer() ).html(
                    pageTotal +'₮ ( Нийт: '+ total +'₮)'
                    );
        }
    } );

});

var empId;
var inputs;
$("#btn_"+section).click(function(){

    inputs = $('input[type="text"]').each(function() {
        $(this).data('original', this.value);
    });
    password = $('input[type="password"]').each(function() {
        $(this).data('original', this.value);
    });
    email = $('input[type="email"]').each(function() {
        $(this).data('original', this.value);
    });
    textarea = $('textarea[type="text"]').each(function() {
        $(this).data('original', this.value);
    });
    number = $('input[type="number"]').each(function() {
        $(this).data('original', this.value);
    });
    $('select[name="branchId"]').each(function() {
        $(this).data('original', this.value);
    });
    $('select[name="gender"]').each(function() {
        $(this).data('original', this.value);
    });
    $('select[name="role"]').each(function() {
        $(this).data('original', this.value);
    });

});


$('#empUpdate_form').submit(function(){
    
    var data = {};
    var check = false;
    data['table'] = "employee";
    data['id'] = empId;

    inputs.each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            //data['username'] = aewrwe
            check = true;
        }
    }); 
    email.each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            //data['username'] = aewrwe
            check = true;
        }
    }); 
    password.each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            check = true;
        }
    }); 
    number.each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            check = true;
        }
    }); 
    $("select[name=gender]").each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            check = true;
        }
    }); 
    $("select[name=role]").each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            check = true;
        }
    }); 
    $("select[name=branchId]").each(function() {
        if ($(this).data('original') !== this.value) {
            data[$(this).attr("name")] = $(this).val();
            check = true;
        }
    }); 

    if (check) {
        data['column'] = "modify";
    }

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

});

$('#editableEmp').on('click', 'tr', function () {

    var oTable = $('#editableEmp').DataTable();
    var data = oTable.row( this ).data();
    empId = data[0];
    alert(section);

    $.ajax({
        url : "getdata.php",
        type: "POST",
        data : { table: section, id: empId},
        success: function(data)
        {
            switch(section) {
                case 'employee':
                    setEmployee(data);
                    break;

                default:
                    // code
            }
            $("#btn_"+section).trigger("click");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        }
    });


} );

$("#btn_empDelete").on("click", function(){
    //alert($(this).attr("del-val"));

			bootbox.dialog({
			  message: "Та энэ хэрэглэгчийн устгах гэж байна?",
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
                        data : { table: section, id: empId}, 
                        success: function(data) { 
                        console.log(data); 
                        window.location.replace("index.php?employee");
                        }, 
                        error: function (jqXHR, textStatus, errorThrown) {
                        } 
                        });
					  /*
					  
					  using $.ajax();
					  
					  $.ajax({
						  
						  type: 'POST',
						  url: 'delete.php',
						  data: 'delete='+pid
						  
					  })
					  .done(function(response){
						  
						  bootbox.alert(response);
						  parent.fadeOut('slow');
						  
					  })
					  .fail(function(){
						  
						  bootbox.alert('Something Went Wrog ....');
						  						  
					  })
					  */
				  }
				}
			  }
			});

});

$('.profit_form').submit(function(){

    //alert($(this).find('input[name=workingId]').val());
    var total = $(this).find('input[name=total_Profit]').val();
    var w_id = $(this).find('input[name=workingId]').val();
    $(this)

    $.ajax({
        url : "workUpdate.php",
        type: "POST",
        data : { value: total, key: w_id },
        success: function(data, textStatus, jqXHR)
        {
            console.log(data);
            /*var result = $.parseJSON(data);
              console.log(result["response"].baih); */

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        }
    });

    //return false;
});

function setEmployee(json){

    $('input[name=username]').val(json[0].username);
    $('input[name=age]').val(json[0].age);
    $('input[name=email]').val(json[0].email);
    $('input[name=phone]').val(json[0].phone);
    $('select[name=role]').val(json[0].role);   
    $('select[name=gender]').val(json[0].gender);
    $('input[name=password]').val(json[0].password);
    $('select[name=branchId]').val(json[0].branchId);
    $('.modal-title').text(json[0].username);

}
