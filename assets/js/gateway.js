function updateDID(){
    var did = $("#extension").find(":selected").val();
    var contact = $("#extension").find(":selected").text();
    contact = contact.split(" - ");
    $("#primarydid").val(did);
    $("#description").val(contact[1]);
}

if($('#ModalImage').length){
    var modal = document.getElementById("ModalImage");
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    var span = document.getElementsByClassName("closeimage")[0];
    span.onclick = function () {
        modal.style.display = "none";
    }

    function image(event)  {    
        modal.style.display = "block";
        modalImg.src = event.target.src;
        captionText.innerHTML = event.target.alt;
    }
}

if( $("#message").length && $("#message").val() != ""){
    fpbxToast($("#message").val(),'Error','error');
}

function escapeHtml(value){
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function statusFormatter(value, row, index) {
    if (value === 'online') {
        return '<i class="fa fa-plug" title="' + i18n.gettext("Connected") + '"></i>';
    }
    if (value === 'unknown') {
        return '<i class="fa fa-question-circle" title="' + i18n.gettext("Unknown") + '"></i>';
    }
    return '<i class="fa fa-bell gw-bell-ring" title="' + i18n.gettext("Disconnected") + '"></i>';
}

function detailFormatter(index, row) {
    var html = []
    html.push('<div class="box" style="padding: 20px;">');
    html.push('<h3 class="subtitle"> - '+i18n.gettext("Details")+' </h3>');
    $.each(row, function (key, value) {
      if(key === "status"){
        return;
      }
      if(key === "email"){
        value = (value === null) ? i18n.gettext("none") : '<a href="mailto:'+ escapeHtml(value) + '">'+escapeHtml(value)+'</a>';
        html.push('<p><b style="text-transform: capitalize;">' + i18n.gettext(key) + ':</b> ' + value + '</p>')
        return;
      }
      if(key === "dids"){
        var count = (value.replace("[","").replace("]","").split(','));
        html.push('<p><b style="text-transform: capitalize;">' + i18n.gettext(key) + ':</b>');
        html.push('<ol style="list-style-type: square;">');
        $.each(count,function(index, val){
            html.push('<li>'+ escapeHtml(val.replaceAll("\"","")) + '</li>');
        });
        html.push('</ol>');
      }
      else{
        html.push('<p><b style="text-transform: capitalize;">' + i18n.gettext(key) + ':</b> ' + escapeHtml(value) + '</p>')
      }
    })
    html.push('</div>');
    return html.join('')
  }

function extensionsFormater(value, row, index) {
    var html = '<a href="?display=gateway&action=edit_gateway&gateway=' + row["extension"] + '"><i class="fa fa-pencil"></i></a>';
    html += '&nbsp;<i class="fa fa-trash" onclick="delGateway(\'' + row["extension"] + '\')"></i>';
    return html;
}

function didsFormater(value, row, index) {
    var count = (value.replace("[","").replace("]","").split(','));
    var detail = "";
    $.each(count,function(index, val){
        detail += (index+1)+" - "+val.replace(/&quot;/g, "")+"\n";
    });
    var html = '<span class="badge badge-light" title="'+detail+'">'+count.length+'</span>';
    return html;
}

function delGateway(gateway){
    if(confirm(i18n.gettext('Do you really want to delete this gateway?'))) {
        $.ajax({
            url: "ajax.php?module=gateway&command=delete&gateway="+gateway,
            dataType:"json",
            success: function (json) {
                if (json.status === true){			
                    $("#gatewayList").bootstrapTable("refresh", "{silent: true}");
                    fpbxToast(i18n.gettext("The gateway has been successfully delete"),'Sucess','success');
                };
            },
            error: function(xhr, status, error) {
                fpbxToast(i18n.gettext("An Ajax error is occured!! Please, check console logs."),'Error','error');
                console.error(xhr, status, error);
            }
        });	
    }	
}

$(document).on('click','.delDid', function(e){
	e.preventDefault();
	var $row = $(this).closest('.did-row');
	$row.fadeOut('normal', function(){ $row.remove(); });
});

$(document).on('click','#cancel', function(){
    window.location.href = "./config.php?display=gateway";
});

/**
 * For JS loc 
 */
function nothing(){
    extension   = i18n.gettext("extension");
    description = i18n.gettext("description");
    contact     = i18n.gettext("contact");
    address     = i18n.gettext("address");
    city        = i18n.gettext("city");
    zip_code    = i18n.gettext("zip_code");
    country     = i18n.gettext("country");
    email       = i18n.gettext("email");
    gateway     = i18n.gettext("gateway");
    dids        = i18n.gettext("dids");
    accountcode  = i18n.gettext("accountcode");
    call_limit   = i18n.gettext("call_limit");
    status       = i18n.gettext("status");
    connected    = i18n.gettext("Connected");
    disconnected = i18n.gettext("Disconnected");
    unknown      = i18n.gettext("Unknown");
}