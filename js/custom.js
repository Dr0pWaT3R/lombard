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
    $('#extendInterest').keyup(function(event) {
        //alert("ajilt bn");
        var loanMoney = parseInt($('#extendLoanMoney').val()); 
        var interest = $('#extendInterest').val();
        var torguuli = Math.round(loanMoney * interest / 100);
        var expiredDay = document.getElementById('extendExpiredDay').innerHTML; 
        //
        $('#extendTorguuli').val(torguuli * expiredDay);
    });
});

var invoiceId;
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
        url : "empUpdate.php",
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
    //return false;

});

$('#editableEmp').on('click', 'tr', function () {

    var oTable = $('#editableEmp').DataTable();
    var data = oTable.row( this ).data();
    empId = data[0];
    //alert(empId);

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

$('.invoiceUpdateBtn').on('click', function() {
    alert($(this).attr('value'));
    invoiceId = $(this).attr('value');

    /* $.ajax({
        url : "getdata.php",
        type: "POST",
        data : {table: section, id: compID},
        success: function(data)
        {
            setCompany(data);
            //alert(data);
            $("#btn_upExpirD").trigger("click");
        }
    }); */

});

$('.invoiceExtendBtn').on('click', function() {
    //alert($(this).attr('value'));
    invoiceId = $(this).attr('value');

    $.ajax({
        url : "getdata.php",
        type: "POST",
        data : {table: section, id: invoiceId},
        success: function(data)
        {
            setMaterial(data);
            //alert(data);
            $("#btn_"+section).trigger("click");
        }
    }); 

});

$('.invoiceDeleteBtn').on('click', function() {
    alert($(this).attr('value'));
    var clientId = $(this).attr('value');

    bootbox.dialog({
        message: "Та энэ панааныг устгах гэж байна?",
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
                        data : { table: section, id: clientId}, 
                        success: function(data) { 
                            console.log(data); 
                            window.location.replace("index.php?invoiceList");
                        }, 
                        error: function (jqXHR, textStatus, errorThrown) {
                        } 
                    });
                }
            }
        }
    });

});

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

    $('input[name=firstname]').val(json[0].firstname);
    $('input[name=lastname]').val(json[0].lastname);
    $('input[name=email]').val(json[0].email);
    $('input[name=phone]').val(json[0].phone);
    $('select[name=role]').val(json[0].role);   
    $('select[name=gender]').val(json[0].gender);
    $('input[name=password]').val(json[0].password);
    $('.modal-title').text(json[0].lastname);

}

function setMaterial(json) {

    $('input[name=extendCreatedAt]').val(json[0].createdAt);
    $('input[name=extendExpiredAt]').val(json[0].expiredAt);
    $('input[name=extendInterest]').val(json[0].interest);
    $('input[name=extendLoanMoney]').val(json[0].loanMoney);
    $('input[name=extendUsedTime]').val(json[0].usedTime);
    if(json[0].expiredDay > 0){
        document.getElementById("extendExpiredDay").textContent = json[0].expiredDay;
    }else{
        document.getElementById("extendExpiredDay").textContent = 'Хугацаа хэтрээгүй';
        $('#extendInterest').attr('readonly', true);
        $('#extendTorguuli').attr('readonly', true);
    }
    document.getElementById("fName").textContent = json[0].firstname;
    document.getElementById("lName").textContent = json[0].lastname;
    document.getElementById("phone").textContent = json[0].phone;
    document.getElementById("rd").textContent = json[0].registerNumber;
    document.getElementById("address").textContent = json[0].address;
    document.getElementById("materialName").textContent = json[0].name;
    document.getElementById("materialId").textContent = json[0].id;
    document.getElementById("number").textContent = json[0].number;
    document.getElementById("gramm").textContent = json[0].gramm;
    document.getElementById("carat").textContent = json[0].carat;
    document.getElementById("sign").textContent = json[0].shinjTemdeg;
    document.getElementById("anhniiUne").textContent = json[0].anhnii_unelgee;
    document.getElementById("unelsenEmp").textContent = json[0].empID;
    if(json[0].invoicePrice == 1) {
        document.getElementById("extendInvoice").textContent = 'Төлсөн';
    }else{
        document.getElementById("extendInvoice").textContent = 'Төлөөгүй';
    }
} 

