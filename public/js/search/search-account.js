jQuery(document).ready(function($) {
    $('#name').keyup(function(){
        makeAjaxRequest();
    });

    function makeAjaxRequest() {
        $.ajax({
            url: urlsearch,
            type: 'get',
            data: {name: $('input#name').val()},
            dataType: 'json',
            success: function(response) {
                var status;
                $('table#resultTable tbody').empty();
                for(var i = 0; i < response.length; i++){
                    if(response[i].accountingMode === "contact"){
                        var accountingMode = "Por Contacto";
                    }
                    if(response[i].accountingMode === "sent"){
                        accountingMode = "Por Envio";
                    }
                    if(response[i].subscriptionEmailMode === "postpaid"){
                        var subscriptionEmailMode = "Pospago";
                    }
                    if(response[i].subscriptionEmailMode === "prepaid"){
                        subscriptionEmailMode = "Prepago";
                    }
                    if(response[i].subscriptionSmsMode === "postpaid"){
                        var subscriptionSmsMode = "Pospago";
                    }
                    if(response[i].subscriptionSmsMode === "prepaid"){
                        subscriptionSmsMode = "Prepago";
                    }
                    if(response[i].status === "0"){
                        var status = "account-disabled";
                    }
                    if(response[i].status === "1"){
                        status = "";
                    }
                    
                    var row = "<tr class='"+status+"'>\n\
                                <td><strong>"+response[i].id+" - "+response[i].name+"</strong></td>\n\
                                <td>"+response[i].prefix+"</td>\n\
                                <td>"+response[i].platforms+"</td>\n\
                                <td>"+response[i].idaccountclassification+"</td>\n\
                                <td>"+accountingMode+"</td>\n\
                                <td>"+subscriptionEmailMode+"</td>\n\
                                <td>"+subscriptionSmsMode+"</td>";
                    row += "<td class='user-actions text-right'>\n\
                                <a class='button shining btn btn-xs-round shining shining-round round-button default-inverted' data-toggle='collapse' href='#collapseDetails"+response[i].id+"' aria-expanded='false' aria-controls='collapseDetails' id='details' data-placement='top' title='Ver detalles'>\n\
                                    <span class='glyphicon glyphicon-collapse-down'></span>\n\
                                </a>\n\
                                <a href='"+urluserlist+"/"+response[i].id+"' class='button shining btn btn-xs-round shining shining-round round-button primary-inverted' data-toggle='tooltip' data-placement='top' title='Lista de Usuarios'>\n\
                                    <span class='glyphicon glyphicon-user'></span>\n\
                                </a>\n\
                                <a href='"+urlaccountedit+"/"+response[i].id+"' class='button shining btn btn-xs-round shining shining-round round-button info-inverted' data-toggle='tooltip' data-placement='top' title='Editar esta Cuenta'>\n\
                                    <span class='glyphicon glyphicon-pencil'></span>\n\
                                </a>\n\
                            </td>\n\
                            </tr>";
                    row += "<tr class='collapse' id='collapseDetails"+response[i].id+"'>\n\
                        <td colspan='8'>\n\
                            <table id='collapse' class='table table-bordered' style='width: 45%;' align='center'>\n\
                                <tbody>\n\
                                    <tr>\n\
                                        <td>\n\
                                            <strong>Remitentes:</strong>\n\
                                        </td>\n\
                                        <td >\n\
                                            "+response[i].sender+"\n\
                                        </td>\n\
                                    </tr>\n\
                                    <tr>\n\
                                        <td>\n\
                                            <strong>Creado:</strong>\n\
                                        </td>\n\
                                        <td>\n\
                                            "+response[i].created+"\n\
                                        </td>\n\
                                    </tr>\n\
                                    <tr>\n\
                                        <td>\n\
                                            <strong>Actualizado:</strong>\n\
                                        </td>\n\
                                        <td>\n\
                                            "+response[i].updated+"\n\
                                        </td>\n\
                                    </tr>\n\
                                </tbody>\n\
                            </table>\n\
                        </td>\n\
                    </tr>";
                    $('table#resultTable').append(row);
                };
            }
        });
    }
});
