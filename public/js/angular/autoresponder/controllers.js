(function () {
    angular.module('autoresponder.controllers', [])
            .filter('propsFilter', function () {
                return function (items, props) {
                    var out = [];
                    if (angular.isArray(items)) {
                        var keys = Object.keys(props);
                        items.forEach(function (item) {
                            var itemMatches = false;
                            for (var i = 0; i < keys.length; i++) {
                                var prop = keys[i];
                                var text = props[prop].toLowerCase();
                                if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                                    itemMatches = true;
                                    break;
                                }
                            }

                            if (itemMatches) {
                                out.push(item);
                            }
                        });
                    } else {
                        out = items;
                    }
                    return out;
                };
            })
            .controller('listController', ['$scope', 'RestServices', 'notificationService', function ($scope, RestServices, notificationService) {
                    $scope.initial = 0;
                    $scope.page = 1;

                    $scope.forward = function () {
                        $scope.initial += 1;
                        $scope.page += 1;
                        $scope.listAutoresponse();
                    };
                    $scope.fastforward = function () {
                        $scope.initial = ($scope.list.total_pages - 1);
                        $scope.page = $scope.list.total_pages;
                        $scope.listAutoresponse();
                    };
                    $scope.backward = function () {
                        $scope.initial -= 1;
                        $scope.page -= 1;
                        $scope.listAutoresponse();
                    };
                    $scope.fastbackward = function () {
                        $scope.initial = 0;
                        $scope.page = 1;
                        $scope.listAutoresponse();
                    };

                    $scope.listAutoresponse = function () {
                        RestServices.getAllAutoresponder($scope.initial, $scope.filter).then(function (res) {
                            $scope.list = res;
                        });
                    };

                    $scope.listAutoresponse();

//-----------------------------------------------
//Filtros
                    $scope.filtername = function () {
                        RestServices.getAllAutoresponder($scope.initial, $scope.filter).then(function (res) {
                            $scope.list = res;
                        });
                    };

//-----------------------------------------------
//Eliminar
                    $scope.confirmDelete = function (id) {
                        $scope.idAutoresponder = id;
                        openModal();
                    };

                    $scope.deleteAutoresponder = function () {
                        RestServices.delete($scope.idAutoresponder).then(function (res) {
                            notificationService.warning(res.message);
                            $scope.listAutoresponse();
                        });
                        closeModal();
                    };

                    $scope.translationDays = function (day) {
                        switch (day) {
                            case 'Monday':
                                day = 'Lunes';
                                break;
                            case 'Tuesday':
                                day = 'Martes';
                                break;
                            case 'Wednesday':
                                day = 'Miercoles';
                                break;
                            case 'Thursday':
                                day = 'Jueves';
                                break;
                            case 'Friday':
                                day = 'Viernes';
                                break;
                            case 'Saturday':
                                day = 'Sabado';
                                break;
                            case 'Sunday':
                                day = 'Domingo';
                                break;
                        }
                        return day;
                    };

                    $scope.translateClass = function (type) {
                        switch (type) {
                            case 'sms':
                                type = 'Mensaje de Texto';
                                break;
                            case 'mail':
                                type = 'Correo Electronico';
                                break;
                        }
                        return type;
                    }

                    $scope.previewAutoresponder = function (idAutoresponder) {
                        $('#modal-body-preview').empty();
                        htmlPreviewAutores(idAutoresponder);
                    };
                }])
            .controller('birthdayController', ['$scope', 'RestServices', 'notificationService', '$window', '$stateParams', function ($scope, RestServices, notificationService, $window, $stateParams) {
                    $scope.data = {};
                    $scope.data.status = true;
                    $scope.boolEditors = true;
                    $scope.insertoption = false;
                    $scope.countContactList="";
                    $scope.addressees = [];
                    $scope.addressees.selectdContactlis = [];
                    $scope.addressees.count = 0;
                    $scope.customfield = [];
                    $scope.idcontactlist;
                    $scope.aligncenter = false;
                    $scope.negrita = false;
                    $scope.alignleft = false;
                    $scope.alignright = false;
                    $scope.align="";                
                    $scope.letra="";
                    $scope.services = [];              
                    $scope.nombreletra;
                    $scope.disabled2 = true;
                    $scope.disabled = true;
                    $scope.disabled3 = true;
                    $scope.optionadvance=0;
                    
                    $scope.mixedField = "";//almacena el nombre del campo personalizado dinamicamente
                    $scope.idCPmixed = 0;//obtiene el id del campo personalizado al crearlo dinamicamente
                    $scope.resCP = "";//guarda la respuesta de la creacion del campo (campos de la tabla)
                    $scope.selectCF = [];//VALORES DE MULTISELECT EVITANDO AUTOSORT
                    $scope.tabs = [];//ARREGLO DE TBS DINAMICOS
                    $scope.textAlign = "left";//ALINEACION DE TEXTO
                    $scope.infoLeft = "";//CSS BOTON LEFT
                    $scope.infoCenter = "";//CSS BOTON CENTER
                    $scope.infoRight = "";//CSS BOTON RIGHT
                    var OLDselectCF = [];
                    var idcustomfield = [];

                    $scope.setAlign = function (prm) {
                        
                        if(prm == 2 && $scope.insertoption == true){
                            if ($('#insertoption').prop('checked')) {
                                $scope.insertoption = true;
                                $scope.optionadvance=1;
                                //si el switch esta en on , se pone el campo en requerido
                                $("#services").attr("required", true);
                                if($scope.addressees.selectdContactlis !== undefined) {
                                    
                                    $scope.countContactList = $scope.addressees.selectdContactlis.length ;
                                    if ($scope.countContactList === 1) {
                                        $('#hiddecontent').css('display','none');
                                        $scope.idcontactlist = $scope.addressees.selectdContactlis[0].idContactlist;
                                        RestServices.findcustomfields($scope.idcontactlist).then(function (data) {
                                            $scope.customfield = data;
                                        }).catch(function (error) {
                                            notificationService.error(error.message);
                                            $scope.insertoption = false;
                                        });
                                        
                                    }else {
                                        $('#hiddecontent').css({"display": "none"});
                                        notificationService.error("Estimado usuario sólo puedes elegir una lista de contacto para esta opción avanzada");                                        
                                        $scope.insertoption = false;
                                        $scope.optionadvance=0;
                                    }
                                } else {
                                    notificationService.error("Estimado usuario debes elegir una lista de contacto");
                                    $('#hiddecontent').css({"display": "none"});
                                    $scope.insertoption = false;
                                    $scope.optionadvance=0;
                                }
                                
    
                            }else{
                              $scope.insertoption = false;
                              $('#insertoption').prop('checked', false); 
                              $('#hiddecontent').css({"display": "block"});  
                            }    
                        }else{                            
                            if($('#advancedoptions').prop('checked') == false) {
                                $scope.insertoption = false;
                                 $('#insertoption').prop('checked', false); 
                            } 
                        }                        
                        
                        if(prm == 2 && $scope.insertoption == false){//OCULTA DE NUEVO EL PANEL DE OPCIONES AVANZADAS Y SETEA TODO EN VACIO
                            $scope.num="";
                            $scope.num2="";
                            $scope.num3="";
                            $scope.disabled = true;
                            $scope.disabled3 = true;
                            $scope.disabled2 = true;
                            $('#selectcolor').val("");
                            $('#result1').css({"color": "black"});
                            $('#result2').css({"color": "black"});
                            $('#result3').css({"color": "black"});
                            $('#result1').css({"font-size": "14px"});
                            $('#result2').css({"font-size": "14px"});
                            $('#result3').css({"font-size": "14px"});
                            $scope.optionadvance=0;                          
                            $('#result3').val("");
                            $('#result2').val("");
                            $('#result1').val("");
                            $('#services').val();
                            $('#services').val("");
                            $('#optionspan').val();
                            $('#hiddecontent').css({"display": "none"});
                            $scope.selectCF=[];
                            $scope.selectCF.length =0;
                            $scope.negrita=false;
                            $('#negrita').removeClass('info-inverted'); 
                            $('.alinear').css({"text-align": "left"});                            
                            $('#check4').prop('checked', false);
                            $('#check3').prop('checked', false);
                            $('#alignright').removeClass('info-inverted');
                            $('#alignright').addClass('default-inverted');
                            $('#aligncenter').removeClass('info-inverted');
                            $('#aligncenter').addClass('default-inverted');
                            $scope.aligncenter = false;
                            $scope.alignright = false;
                            $('#alignleft').addClass('info-inverted');
                            $scope.aligncenter = false;
                            $scope.alignright = false;
                            $all_LI = $("span.spanLC").find("ul").find("li");
                            $all_LI.remove();
                            $scope.tabs = [];
                            OLDselectCF = [];
                            idcustomfield = [];
                            $scope.selectCF = [];        
                        }
                    };
                    //FUNCION PARA EXTRAER POSICION DE ARRELO QUE CONTIENE UN OBJETO
                    function findWithAttr(array, attr, value) {
                        for(var i = 0; i < array.length; i += 1) {
                            if(array[i][attr] === value) {
                                return i;
                            }
                        }
                        return -1;
                    }
                    //FUNCION PARA GUARDAR EL CAMPO PERSONALIZADO COMBINADO AUTOMATICAMENTE
                    $scope.saveCP = function(name){
                        var data = {
                            id : $scope.idCPmixed,
                            name : name,
                            idContactList : $scope.idcontactlist
                        };
                        RestServices.addcustomfield(data).then(function (res) {
                            $scope.idCPmixed = res.customfield.idCustomfield;
                            $scope.resCP = res.customfield;
                            //notificationService.success(res.message);
                      }).catch(function (res) {
                        $scope.btndisabled = false;
                        notificationService.error(res.message);
                      });
                    };
                                                          
                    //Funcion escoger Campos personalizados (maximo 3)
                    $scope.limitfields = function (num){//NUM ES 1 PARA CREACION Y 2 PARA EDICION, ESTO CON EL FIN DE CONTROLAR LA CREACION DE CFMIXED
                        $scope.mixedField = "";
                        var str = "", x = 0;
                        
                        if($scope.services){
                            if($scope.services.length > 0){
                                                   
                                //VALIDAR EL NUMERO DE selectCF Y EL NUMERO DE TABS SI selectCF ES MENOR A TABS ELIMINAR EL TAB INDICADO SI NO AGREGAR
                                if($scope.services.length < $scope.tabs.length){
                                    
                                    //BUSCAMOS LA DIFERENCIA ENTRE LOS CF SELECCINADOS Y LOS QUE ESTABAN ANTES
                                    var difference = OLDselectCF.filter(z => !$scope.services.includes(z));
                                    //EN CUSTOMFIELD (DATA DE SQL) BUSCAMOS EL NOMBRE SEGUN EL ID DEL CF Y LO ELIMINAMOS DE LOS TABS
                                    var searchCustomfield = findWithAttr(idcustomfield,'idcustomfield',difference[0]);
                                    $scope.tabs.splice(searchCustomfield, 1);
                                    //TAMBIEN ELIMINAMOS LA POSICION DEL ARREGLO CON LOS CF
                                    $scope.selectCF.splice(searchCustomfield, 1);
                                    
                                    //ACTUALIZAMOS LA VARIABLE VIEJA CON LA NUEVA INFO SELECCIONADA
                                    OLDselectCF = $scope.services;
                                    
                                    //RECORREMOS LOS TABS PARA ACTUALIZAR CFMIXED
                                    for(var i=0; i<$scope.tabs.length; i++){
                                        str += $scope.tabs[i].nameService+".";
                                    }
                                    
                                    //ENVIAMOS LAS VARIABLES DE CFMIXED
                                    var concat = str.substring(0, str.length-1);
                                    $scope.mixedField = "%%"+concat.toUpperCase()+"%%";
                                    
                                }else{
                                    //AGREGAR EL ULTIMO SELECCIONADO PARA QUE EL SORT AUTOMATICO DEL MULTISELECT NO AFECTE EL ORDEN
                                    var diff = $scope.services.filter(i => !$scope.selectCF.includes(i));
                                    if(diff[0]){
                                       $scope.selectCF.push(diff[0]); 
                                    }
                                    idcustomfield = [];
                                    //BUSCAMOS EN EL ARRELO CUSTOMFIELD EL ALTERNATIVE NAME SEGUN EL SELECCIONADO, AGREGAMOS EL NOMBRE EN CFMIXED
                                    for(var i=0; i<$scope.selectCF.length; i++){
                                        //VALIDAMOS LOS CAMPOS POR DEFECTOS QUE CONTIENEN LOS CONTACTOS
                                        if($scope.selectCF[i] == 0){
                                            str += "NOMBRE"+".";
                                            idcustomfield.push({idcustomfield : "0"});
                                        }else if($scope.selectCF[i] == 1){
                                            str += "APELLIDO"+".";
                                            idcustomfield.push({idcustomfield : "1"});
                                        }else if($scope.selectCF[i] == 2){
                                            str += "FECHA_NACIMIENTO"+".";
                                            idcustomfield.push({idcustomfield : "2"});
                                        }else{
                                            x = findWithAttr($scope.customfield,'idCustomfield',$scope.selectCF[i]);
                                            str += $scope.customfield[x].alternativename+".";
                                            idcustomfield.push({idcustomfield : $scope.customfield[x].idCustomfield});
                                        }
                                        
                                    }
                                    
                                    //BUSCAMOS LA DIFERENCIA ENTRE LA SELECCION VIEJA Y LA NUEVA 
                                    var difference = $scope.selectCF.filter(z => !OLDselectCF.includes(z));
                                    
                                    if(difference[0]){
                                       //EN CUSTOMFIELD (DATA DE SQL) BUSCAMOS EL NOMBRE SEGUN EL ID DEL CF Y LO ENVIAMOS A LOS TABS
                                        var searchCustomfield = findWithAttr($scope.customfield,'idCustomfield',difference[0]);
                                        $scope.tabs.push({idcustomfield : diff[0], nameService:$scope.customfield[searchCustomfield].name, disabled: false});
                                    }
                                        
                                    
                                    //ACTUALIZAMOS LA VARIABLE VIEJA CON LA NUEVA INFO SELECCIONADA
                                    OLDselectCF.push(diff[0]);
                                    
                                    //ENVIAMOS LAS VARIABLES DE CFMIXED
                                    var concat = str.substring(0, str.length-1);
                                    $scope.mixedField = "%%"+concat.toUpperCase()+"%%";
                                }
                                if(num == 1){
                                    $scope.saveCP($scope.mixedField);
                                }
                            }else{
                                //SI ENTRA AQUI ES PORQUE NO HAY NINGUN CF SELECCIONADO EN EL ELEMENTO SELECT ENTONCES VACIAMOS LAS VARIABLES DINAMICAS
                                $scope.tabs = [];
                                OLDselectCF = [];
                                idcustomfield = [];
                                $scope.selectCF = [];
                            }
                        }else{
                            //SI ENTRA AQUI ES PORQUE NO HAY NINGUN CF SELECCIONADO EN EL ELEMENTO SELECT ENTONCES VACIAMOS LAS VARIABLES DINAMICAS
                            $scope.tabs = [];
                            OLDselectCF = [];
                            idcustomfield = [];
                            $scope.selectCF = [];
                        }
                         
                    };
                    
                    //CAMBIAR TAMAÑO DE LA LETRA RECIBIENDO EL INDICE Y ATRIBUTOS DE INPUT EN ARREGLO DE TABS
                    $scope.changeSize = function (index, value) {
                        $scope.tabs[index].fontSize = value.selectedOption;
                    };

                    //CAMBIAR COLOR DE LA LETRA RECIBIENDO EL INDICE Y ATRIBUTOS DE INPUT EN ARREGLO DE TABS
                    $scope.changeColor = function (index, value) {  
                        $scope.tabs[index].color = value.selectColor;                
                    };
                                        
                    //CAMBIAR ESTILO DE LA LETRA RECIBIENDO EL INDICE Y ATRIBUTOS DE INPUT EN ARREGLO DE TABS
                    $scope.changeStyle = function (index, value) {                    

                        if(value.bold){
                            $scope.tabs[index].fontWeight = "bold";
                            $scope.tabs[index].boldClass = "info-inverted";
                        }else{
                            $scope.tabs[index].fontWeight = "";
                            $scope.tabs[index].boldClass = "";
                        }
                        
                        if(value.italics){
                            $scope.tabs[index].fontStyle = "italic";
                            $scope.tabs[index].italicsClass = "info-inverted";
                        }else{
                            $scope.tabs[index].fontStyle = "";
                            $scope.tabs[index].italicsClass = "";
                        }
                        
                        if(value.underlined){
                            $scope.tabs[index].textDecoration = "underline";
                            $scope.tabs[index].underlinedClass = "info-inverted";
                        }else{
                            $scope.tabs[index].textDecoration = "";
                            $scope.tabs[index].underlinedClass = "";
                        }             
                        
                    };
                    
                    //CAMBIAR FUENTE DE LA LETRA RECIBIENDO EL INDICE Y ATRIBUTOS DE INPUT EN ARREGLO DE TABS
                    $scope.changeFont = function (index, value){
                        $scope.tabs[index].fontFamily = value.fontFamily; 
                    };
                    
                    //CAMBIAR ALINEACION DE LA LETRA RECIBIENDO EL INDICE Y ATRIBUTOS DE INPUT EN ARREGLO DE TABS
                    $scope.alignText =  function (value){
                        
                        if(value.left){
                            $scope.textAlign = "left";
                            $scope.infoLeft = "info-inverted";
                        }else if(value.center){
                            $scope.textAlign = "center";
                            $scope.infoCenter = "info-inverted";
                        }else if(value.right){
                            $scope.textAlign = "right";
                            $scope.infoRight = "info-inverted";
                        }else{
                            $scope.textAlign = "";
                            $scope.infoLeft = "";
                            $scope.infoCenter = "";
                            $scope.infoRight = "";
                        }
                        
                    };
                    $scope.serviceSelected = [];          
                    if ($stateParams.id) {
                        RestServices.getAutoresponder($stateParams.id).then(function (res) {
                            $scope.data = res.autoresponder;
                            $("#valueDatepicker").val(res.autoresponder.time);
                            $scope.data.senderNameSelect = res.autoresponder.idNameSender;
                            $scope.data.senderMailSelect = res.autoresponder.idEmailsender;
                            $scope.data.status = (res.autoresponder.status == 1);
                            if (res.autoresponder.target) {
                                $scope.addressees.showstep1 = false;
                                var json = jQuery.parseJSON(res.autoresponder.target);
                                $scope.addressees.count = res.autoresponder.quantitytarget;
                                $scope.addressees.condition = json.condition;
                                $scope.filters = json.filters;
                                if (json.type == "contactlist") {
                                    $scope.addressees.showContactlist = false;
                                    $scope.addressees.selectdContactlis = json.contactlists;
                                    $scope.getAllContactlist();

                                } else if (json.type == "segment") {
                                    $scope.addressees.showSegment = false;
                                    $scope.addressees.selectdSegment = json.segment;
                                    $scope.getAllSegment();
                                }
                            }

                            //RECUPERAMOS LA INFORMACION DE OPTIONADVANCE
                            if(res.autoresponder.optionAdvance == 1){
                                $scope.optionadvance = res.autoresponder.optionAdvance;
                                $scope.advancedoptions = true;
                                $scope.insertoption = true;
                                $('#advancedoptions').prop('checked', true);
                                $('#insertoption').prop('checked', true);
                                $scope.setAlign(2);
                            }
                            
                            //RECUPERAMOS LA INFORMACION DE CUSTOMFIELDS Y LA RECORREMOS
                            if(res.autoresponder.customFields){                            
                                var obj= JSON.parse(res.autoresponder.customFields);

                                Object.keys(obj).forEach(function (key) {
                                    var value = obj[key];
                                    var indiceCF =0;
                                    
                                    if(key == "customFields"){
                                        var valueArrCF = value;
                                        RestServices.findcustomfields($scope.idcontactlist).then(function (data) {
                                            $scope.customfield = data;
                                            for (var x=Object.keys(valueArrCF),i=0;i<x.length,keyCF=x[i],valueCF=valueArrCF[keyCF];i++){
                                                
                                                $scope.services.push(keyCF);
                                                $scope.limitfields(2);
                                                //INSERTAMOS LOS VALORES DE TABS DESPUES DE CREAR LA POSICION EN LA FUNCION limitfields
                                                $scope.tabs[i].idcustomfield = keyCF;
                                                $scope.tabs[i].fontSize = valueCF.fontSize;
                                                $scope.tabs[i].color = valueCF.color;
                                                $scope.tabs[i].fontWeight = valueCF.fontWeight;
                                                $scope.tabs[i].fontStyle = valueCF.fontStyle;
                                                $scope.tabs[i].textDecoration = valueCF.textDecoration;
                                                $scope.tabs[i].fontFamily = valueCF.fontFamily;

                                            }
                                            
                                            $("#services").val($scope.services).trigger("change.select2");
                                                                            
                                        }).catch(function (error) {
                                            notificationService.error(error.message);
                                            $scope.insertoption = false;
                                        });
                                        
                                    }
                                    
                                    if(key == "idCFmixed"){
                                        $scope.idCPmixed = value;
                                    }
                                    
                                    if(key == "textAlign"){
                                        
                                        $scope.textAlign = value;

                                        if(value == "left"){
                                            $scope.textAlign = "left";
                                            $scope.infoLeft = "info-inverted";
                                        }else if(value == "center"){
                                            $scope.textAlign = "center";
                                            $scope.infoCenter = "info-inverted";
                                        }else if(value == "right"){
                                            $scope.textAlign = "right";
                                            $scope.infoRight = "info-inverted";
                                        }
                                        
                                    }
                                    
                                });
                                
                            }

                            if (!res.autoresponder.autorespondercontent) {
                                $scope.boolEditors = true;
                            } else {
                                $scope.boolEditors = false;
                                htmlPreview($stateParams.id);
                                $scope.getUrl = res.autoresponder.autorespondercontent.url;
                            }
                            
                        });
                    }


                    $scope.addressees = {selectdContactlis: []};
                    $scope.addressees = {selectdSegment: []};
                    $scope.addressees.showSegment = true;
                    $scope.addressees.showstep1 = true;
                    $scope.addressees.showContactlist = true;


                    $('#dateInitial').datetimepicker({
                        format: 'hh:mm',
                        language: 'es'
                    }).on('changeDate', function (ev) {
                        $scope.$apply();
                    });
                    
                    $scope.saveAutoresponder = function () {/*************************************************************************/
                        
                        //VALIDAMOS QUE SE HAYA ELEGIDO UNA PLANTILLA ANTES DE GUARDAR LA AUTORESPUESTA
                        if($scope.boolEditors == true){
                            notificationService.error("Por favor seleccione una plantilla.");
                            return;
                        }
                        
                        var template = $('#frame').contents().find('body').html();
                        var patt = new RegExp($scope.mixedField);
                        var searchSTR = patt.test(template);
                        if(!searchSTR){
                            notificationService.error("Por favor ingresa el nombre del campo personalizado en la plantilla.");
                            return;
                        }
                        
                         
                        if($scope.optionadvance != 0 ){
                            
                            if($scope.services.length >= 2){
                                
                                $scope.data.optionAdvance = $scope.optionadvance;
                                
                                if(!$scope.data.customFields){//ENTRA CUANDO ESTA CREANDO
                                    $scope.data.customFields = [];
                                }else{//ENTRA CUANDO ESTA EDITANDO
                                    $scope.data.customFields = [];
                                }
                                
                                //RECORREMOS EL ARREGLO DE TABS PARA EXTRAER LA INFO NECESARIA PARA EL JSON
                                for(var i=0; i < $scope.tabs.length; i++){
    
                                    //SI EL TAMAÑO DE FUENTE NO FUE SELECCIONADO PONEMOS POR DEFECTO 12
                                    if(!$scope.tabs[i].fontSize){
                                        $scope.tabs[i].fontSize = "12";
                                    }
                                    //SI EL COLOR NO FUE SELECCIONADO PONEMOS POR DEFECTO NEGRO
                                    if(!$scope.tabs[i].color){
                                        $scope.tabs[i].color = "#000000";
                                    }
                                    //SI NEGRITA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontWeight){
                                        $scope.tabs[i].fontWeight = "unset";
                                    }
                                    //SI CURSIVA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontStyle){
                                        $scope.tabs[i].fontStyle = "unset";
                                    }
                                    //SI SUBRAYADO NO FUE SELECCIONADO PONEMOS POR DEFECTO NONE
                                    if(!$scope.tabs[i].textDecoration){
                                        $scope.tabs[i].textDecoration = "none";
                                    }
                                    //SI TIPO DE LETRA NO FUE SELECCIONADO PONEMOS POR DEFECTO INHERIT
                                    if(!$scope.tabs[i].fontFamily){
                                        $scope.tabs[i].fontFamily = "inherit";
                                    }
                                    //$scope.tabs[i].idcustomfield
                                    $scope.data.customFields.push({
                                            
                                            idCF : $scope.tabs[i].idcustomfield,
                                            fontSize: $scope.tabs[i].fontSize, 
                                            color: $scope.tabs[i].color, 
                                            fontWeight: $scope.tabs[i].fontWeight, 
                                            fontStyle: $scope.tabs[i].fontStyle,
                                            textDecoration: $scope.tabs[i].textDecoration,
                                            fontFamily: $scope.tabs[i].fontFamily
                                            
                                    });
                                }
                                $scope.data.textAlign = $scope.textAlign;
                                                                
                                if(!$scope.resCP && $scope.idCPmixed == 0){
                                    $scope.data.idCFmix = "";
                                }
                                if(!$scope.resCP && $scope.idCPmixed != 0){
                                    $scope.data.idCFmix = $scope.idCPmixed;
                                }else{
                                    $scope.data.idCFmix = $scope.resCP.idCustomfield;
                                }   
                            }else{
                                notificationService.error("Para crear autrespuesta con opciones avanzadas debe seleccionar al menos 2 campos personalizados.");
                                return;
                            }
                            
                        }else{
                            $scope.data.customFields = null;
                        }
                        
                         
                        $scope.data.addressees = $scope.addressees; 
                        $scope.data.time = $("#valueDatepicker").val();
                        $scope.data.filters = $scope.filters;
                        $scope.data.condition = $scope.addressees.condition;
                        var idAutoresponder = $stateParams.id ? $stateParams.id : 0;

                        RestServices.createAutoresponder($scope.data, idAutoresponder).then(function (res) {
                            notificationService.success(res['message']);
                            $window.location.href = fullUrlBase + templateBase + "#/";
                        }).catch(function (error) {
                            notificationService.error(error.message);
                            return;
                        });
                    };

                    $scope.clearSelect = function () {
                        $scope.filters = [];
                        $scope.addressees.count = 0;
                        $scope.disabledContactlist = false;
                        $scope.disabledSegment = false;
                        $scope.addressees.selectdContactlis = "";
                        $scope.addressees.selectdSegment = "";
                        $scope.insertoption=false;
                    };
                    $scope.getContactlist = function () {
                        $scope.addressees = {selectdSegment: []};
                        $scope.segments = [];
                        $scope.filters = [];
                        $scope.contactlists = [];
                        $scope.addressees.showstep1 = false;
                        $scope.addressees.count = 0;
                        $scope.addressees.showSegment = true;
                        $scope.addressees.showContactlist = false;
                        $scope.prueba = undefined;  
                        $scope.getAllContactlist();
                        $scope.getAllSegment();
                    };
                    $scope.getAllContactlist = function () {
                        RestServices.getContactlist().then(function (data) {
                            $scope.contactlists = data;
                        });
                    };
                    $scope.getSegment = function () {
                        $scope.addressees = {selectdContactlis: []};
                        $scope.segments = [];
                        $scope.contactlists = [];
                        $scope.filters = [];
                        $scope.addressees.showstep1 = false;
                        $scope.addressees.count = 0;
                        $scope.prueba = undefined;
                        $scope.prueba2 = undefined;
                        $scope.addressees.selectdContactlis = [];
                        $scope.addressees.selectdSegment = [];
                        $scope.addressees.showSegment = false;
                        $scope.addressees.showContactlist = true;
                        $scope.getAllSegment();
                        $scope.getAllContactlist();
                    };

                    $scope.getAllSegment = function () {
                        RestServices.getSegment().then(function (data) {
                            $scope.segments = data;
                        });
                    };

                    $scope.selectAction = function () {
                        $scope.countContacts("contactlist");
                    };

                    $scope.selectActionSegment = function () {
                        $scope.countContacts("segment");
                    };

                    $scope.allSegment = function () {
                        $scope.disabledSegment = true;
                        $scope.addressees.selectdSegment = $scope.segments;
                        $scope.countContacts("segment");
                    };

                    $scope.allContactlist = function () {
                        $scope.disabledContactlist = true;
                        $scope.addressees.selectdContactlis = $scope.contactlists;
                        $scope.countContacts("contactlist");
                    };

                    $scope.countContacts = function (type) {
                        $scope.addressees.count = 0;
                        var data = {
                            type: type,
                            segment: $scope.addressees.selectdSegment,
                            contactlist: $scope.addressees.selectdContactlis,
                            filters: $scope.filters,
                            condition: $scope.addressees.condition,
                            idMail: $scope.idMail
                        };
                        RestServices.countContact(data).then(function (data) {
                            $scope.addressees.count = data;
                        });
                    };

                    function getemailname() {
                        RestServices.getemailname().then(function (res) {
                            $scope.emailname = res;
                        });
                    }

                    function getemailsend() {
                        RestServices.getemailsend().then(function (res) {
                            $scope.emailsend = res;
                        });
                    }

                    getemailname();
                    getemailsend();

                    $scope.showInputName = false;
                    $scope.showSelectName = true;
                    $scope.showIconsName = true;
                    $scope.showIconsSaveName = false;

                    $scope.changeStatusInputName = function () {
                        if (!$scope.showInputName) {
                            $scope.showInputName = true;
                            $scope.showSelectName = false;
                            $scope.showIconsName = false;
                            $scope.showIconsSaveName = true;
                        } else {
                            $scope.showInputName = false;
                            $scope.showSelectName = true;
                            $scope.showIconsName = true;
                            $scope.showIconsSaveName = false;
                        }
                    };

                    $scope.showInputEmail = false;
                    $scope.showSelectEmail = true;
                    $scope.showIconsEmail = true;
                    $scope.showIconsSaveEmail = false;

                    $scope.changeStatusInputEmail = function () {
                        if (!$scope.showInputEmail) {
                            $scope.showInputEmail = true;
                            $scope.showSelectEmail = false;
                            $scope.showIconsEmail = false;
                            $scope.showIconsSaveEmail = true;
                        } else {
                            $scope.showInputEmail = false;
                            $scope.showSelectEmail = true;
                            $scope.showIconsEmail = true;
                            $scope.showIconsSaveEmail = false;
                        }
                    };

                    $scope.saveName = function () {
                        var data = {name: $scope.senderName};

                        RestServices.addEmailName(data).then(function (res) {
                            notificationService.success(res['msg']);
                            $scope.senderName = "";
                            getemailname();
                            $scope.changeStatusInputName();
                            $scope.data.senderNameSelect = res['idNameSender'];
                        });
                    };

                    $scope.saveEmail = function () {
                        var data = {email: $scope.senderMail};

                        RestServices.addEmailSender(data).then(function (res) {
                            notificationService.success(res['msg']);
                            $scope.senderMail = "";
                            getemailsend();
                            $scope.changeStatusInputEmail();
                            $scope.data.senderMailSelect = res['idEmailsender'];
                        });
                    };

                    $scope.openMailTemplate = function () {
                        
                        if($scope.optionadvance != 0 ){
                            
                            if($scope.services.length >= 2){
                                
                                $scope.data.optionAdvance = $scope.optionadvance;
                                
                                if(!$scope.data.customFields){//ENTRA CUANDO ESTA CREANDO
                                    $scope.data.customFields = [];
                                }else{//ENTRA CUANDO ESTA EDITANDO
                                    $scope.data.customFields = [];
                                }
                                
                                //RECORREMOS EL ARREGLO DE TABS PARA EXTRAER LA INFO NECESARIA PARA EL JSON
                                for(var i=0; i < $scope.tabs.length; i++){
                        
                                    //SI EL TAMAÑO DE FUENTE NO FUE SELECCIONADO PONEMOS POR DEFECTO 12
                                    if(!$scope.tabs[i].fontSize){
                                        $scope.tabs[i].fontSize = "12";
                                    }
                                    //SI EL COLOR NO FUE SELECCIONADO PONEMOS POR DEFECTO NEGRO
                                    if(!$scope.tabs[i].color){
                                        $scope.tabs[i].color = "#000000";
                                    }
                                    //SI NEGRITA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontWeight){
                                        $scope.tabs[i].fontWeight = "unset";
                                    }
                                    //SI CURSIVA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontStyle){
                                        $scope.tabs[i].fontStyle = "unset";
                                    }
                                    //SI SUBRAYADO NO FUE SELECCIONADO PONEMOS POR DEFECTO NONE
                                    if(!$scope.tabs[i].textDecoration){
                                        $scope.tabs[i].textDecoration = "none";
                                    }
                                    //SI TIPO DE LETRA NO FUE SELECCIONADO PONEMOS POR DEFECTO INHERIT
                                    if(!$scope.tabs[i].fontFamily){
                                        $scope.tabs[i].fontFamily = "inherit";
                                    }
                                    //$scope.tabs[i].idcustomfield
                                    $scope.data.customFields.push({
                                            
                                            idCF : $scope.tabs[i].idcustomfield,
                                            fontSize: $scope.tabs[i].fontSize, 
                                            color: $scope.tabs[i].color, 
                                            fontWeight: $scope.tabs[i].fontWeight, 
                                            fontStyle: $scope.tabs[i].fontStyle,
                                            textDecoration: $scope.tabs[i].textDecoration,
                                            fontFamily: $scope.tabs[i].fontFamily
                                            
                                    });
                                }
                                $scope.data.textAlign = $scope.textAlign;
                                if(!$scope.resCP && $scope.idCPmixed == 0){
                                    $scope.data.idCFmix = "";
                                }
                                if(!$scope.resCP && $scope.idCPmixed != 0){
                                    $scope.data.idCFmix = $scope.idCPmixed;
                                }else{
                                    $scope.data.idCFmix = $scope.resCP.idCustomfield;
                                }  
                            }else{
                                notificationService.error("Para crear autrespuesta con opciones avanzadas debe seleccionar al menos 2 campos personalizados.");
                                return;
                            }
                            
                        }else{
                            $scope.data.customFields = null;
                        }
                        
                        $scope.data.addressees = $scope.addressees;
                        $scope.data.filters = $scope.filters;
                        $scope.data.condition = $scope.addressees.condition;
                        $scope.data.time = $("#valueDatepicker").val();
                        var idAutoresponder = $stateParams.id ? $stateParams.id : 0;

                        RestServices.createAutoresponder($scope.data, idAutoresponder).then(function (res) {
                            $window.location.href = fullUrlBase + 'mailtemplate#/selectautoresponder/' + res.autoresponder.idAutoresponder;
                        });
                    };

                    $scope.openContentEditor = function () {
                        
                        if($scope.optionadvance != 0 ){
                            
                            if($scope.services.length >= 2){
                                
                                $scope.data.optionAdvance = $scope.optionadvance;
                                
                                if(!$scope.data.customFields){//ENTRA CUANDO ESTA CREANDO
                                    $scope.data.customFields = [];
                                }else{//ENTRA CUANDO ESTA EDITANDO
                                    $scope.data.customFields = [];
                                }
                                
                                //RECORREMOS EL ARREGLO DE TABS PARA EXTRAER LA INFO NECESARIA PARA EL JSON
                                for(var i=0; i < $scope.tabs.length; i++){
                        
                                    //SI EL TAMAÑO DE FUENTE NO FUE SELECCIONADO PONEMOS POR DEFECTO 12
                                    if(!$scope.tabs[i].fontSize){
                                        $scope.tabs[i].fontSize = "12";
                                    }
                                    //SI EL COLOR NO FUE SELECCIONADO PONEMOS POR DEFECTO NEGRO
                                    if(!$scope.tabs[i].color){
                                        $scope.tabs[i].color = "#000000";
                                    }
                                    //SI NEGRITA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontWeight){
                                        $scope.tabs[i].fontWeight = "unset";
                                    }
                                    //SI CURSIVA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontStyle){
                                        $scope.tabs[i].fontStyle = "unset";
                                    }
                                    //SI SUBRAYADO NO FUE SELECCIONADO PONEMOS POR DEFECTO NONE
                                    if(!$scope.tabs[i].textDecoration){
                                        $scope.tabs[i].textDecoration = "none";
                                    }
                                    //SI TIPO DE LETRA NO FUE SELECCIONADO PONEMOS POR DEFECTO INHERIT
                                    if(!$scope.tabs[i].fontFamily){
                                        $scope.tabs[i].fontFamily = "inherit";
                                    }
                                    //$scope.tabs[i].idcustomfield
                                    $scope.data.customFields.push({
                                            
                                            idCF : $scope.tabs[i].idcustomfield,
                                            fontSize: $scope.tabs[i].fontSize, 
                                            color: $scope.tabs[i].color, 
                                            fontWeight: $scope.tabs[i].fontWeight, 
                                            fontStyle: $scope.tabs[i].fontStyle,
                                            textDecoration: $scope.tabs[i].textDecoration,
                                            fontFamily: $scope.tabs[i].fontFamily
                                            
                                    });
                                }
                                $scope.data.textAlign = $scope.textAlign;
                                if(!$scope.resCP && $scope.idCPmixed == 0){
                                    $scope.data.idCFmix = "";
                                }
                                if(!$scope.resCP && $scope.idCPmixed != 0){
                                    $scope.data.idCFmix = $scope.idCPmixed;
                                }else{
                                    $scope.data.idCFmix = $scope.resCP.idCustomfield;
                                }   
                            }else{
                                notificationService.error("Para crear autrespuesta con opciones avanzadas debe seleccionar al menos 2 campos personalizados.");
                                return;
                            }
                            
                        }else{
                            $scope.data.customFields = null;
                        }
                        
                        $scope.data.addressees = $scope.addressees;
                        $scope.data.filters = $scope.filters;
                        $scope.data.condition = $scope.addressees.condition;
                        $scope.data.time = $("#valueDatepicker").val();
                        var idAutoresponder = $stateParams.id ? $stateParams.id : 0;

                        RestServices.createAutoresponder($scope.data, idAutoresponder).then(function (res) {
                            $window.location.href = fullUrlBase + templateBase + '/contenteditor/' + res.autoresponder.idAutoresponder;
                        });
                    };

                    $scope.openEditorHtml = function () {
                        
                        if($scope.optionadvance != 0 ){
                            
                            if($scope.services.length >= 2){
                                
                                $scope.data.optionAdvance = $scope.optionadvance;
                                
                                if(!$scope.data.customFields){//ENTRA CUANDO ESTA CREANDO
                                    $scope.data.customFields = [];
                                }else{//ENTRA CUANDO ESTA EDITANDO
                                    $scope.data.customFields = [];
                                }
                                
                                //RECORREMOS EL ARREGLO DE TABS PARA EXTRAER LA INFO NECESARIA PARA EL JSON
                                for(var i=0; i < $scope.tabs.length; i++){
                        
                                    //SI EL TAMAÑO DE FUENTE NO FUE SELECCIONADO PONEMOS POR DEFECTO 12
                                    if(!$scope.tabs[i].fontSize){
                                        $scope.tabs[i].fontSize = "12";
                                    }
                                    //SI EL COLOR NO FUE SELECCIONADO PONEMOS POR DEFECTO NEGRO
                                    if(!$scope.tabs[i].color){
                                        $scope.tabs[i].color = "#000000";
                                    }
                                    //SI NEGRITA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontWeight){
                                        $scope.tabs[i].fontWeight = "unset";
                                    }
                                    //SI CURSIVA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontStyle){
                                        $scope.tabs[i].fontStyle = "unset";
                                    }
                                    //SI SUBRAYADO NO FUE SELECCIONADO PONEMOS POR DEFECTO NONE
                                    if(!$scope.tabs[i].textDecoration){
                                        $scope.tabs[i].textDecoration = "none";
                                    }
                                    //SI TIPO DE LETRA NO FUE SELECCIONADO PONEMOS POR DEFECTO INHERIT
                                    if(!$scope.tabs[i].fontFamily){
                                        $scope.tabs[i].fontFamily = "inherit";
                                    }
                                    //$scope.tabs[i].idcustomfield
                                    $scope.data.customFields.push({
                                            
                                            idCF : $scope.tabs[i].idcustomfield,
                                            fontSize: $scope.tabs[i].fontSize, 
                                            color: $scope.tabs[i].color, 
                                            fontWeight: $scope.tabs[i].fontWeight, 
                                            fontStyle: $scope.tabs[i].fontStyle,
                                            textDecoration: $scope.tabs[i].textDecoration,
                                            fontFamily: $scope.tabs[i].fontFamily
                                            
                                    });
                                }
                                $scope.data.textAlign = $scope.textAlign;
                                if(!$scope.resCP && $scope.idCPmixed == 0){
                                    $scope.data.idCFmix = "";
                                }
                                if(!$scope.resCP && $scope.idCPmixed != 0){
                                    $scope.data.idCFmix = $scope.idCPmixed;
                                }else{
                                    $scope.data.idCFmix = $scope.resCP.idCustomfield;
                                }    
                            }else{
                                notificationService.error("Para crear autrespuesta con opciones avanzadas debe seleccionar al menos 2 campos personalizados.");
                                return;
                            }
                            
                        }else{
                            $scope.data.customFields = null;
                        }
                        
                        $scope.data.addressees = $scope.addressees;
                        $scope.data.filters = $scope.filters;
                        $scope.data.condition = $scope.addressees.condition;
                        $scope.data.time = $("#valueDatepicker").val();
                        var idAutoresponder = $stateParams.id ? $stateParams.id : 0;

                        RestServices.createAutoresponder($scope.data, idAutoresponder).then(function (res) {
                            $window.location.href = fullUrlBase + templateBase + '/contenthtml/' + res.autoresponder.idAutoresponder;
                        });
                    };

                    $scope.editContentEditor = function () {
                        
                        if($scope.optionadvance != 0 ){
                            
                            if($scope.services.length >= 2){
                                
                                $scope.data.optionAdvance = $scope.optionadvance;
                                
                                if(!$scope.data.customFields){//ENTRA CUANDO ESTA CREANDO
                                    $scope.data.customFields = [];
                                }else{//ENTRA CUANDO ESTA EDITANDO
                                    $scope.data.customFields = [];
                                }
                                
                                //RECORREMOS EL ARREGLO DE TABS PARA EXTRAER LA INFO NECESARIA PARA EL JSON
                                for(var i=0; i < $scope.tabs.length; i++){
                        
                                    //SI EL TAMAÑO DE FUENTE NO FUE SELECCIONADO PONEMOS POR DEFECTO 12
                                    if(!$scope.tabs[i].fontSize){
                                        $scope.tabs[i].fontSize = "12";
                                    }
                                    //SI EL COLOR NO FUE SELECCIONADO PONEMOS POR DEFECTO NEGRO
                                    if(!$scope.tabs[i].color){
                                        $scope.tabs[i].color = "#000000";
                                    }
                                    //SI NEGRITA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontWeight){
                                        $scope.tabs[i].fontWeight = "unset";
                                    }
                                    //SI CURSIVA NO FUE SELECCIONADO PONEMOS POR DEFECTO UNSET
                                    if(!$scope.tabs[i].fontStyle){
                                        $scope.tabs[i].fontStyle = "unset";
                                    }
                                    //SI SUBRAYADO NO FUE SELECCIONADO PONEMOS POR DEFECTO NONE
                                    if(!$scope.tabs[i].textDecoration){
                                        $scope.tabs[i].textDecoration = "none";
                                    }
                                    //SI TIPO DE LETRA NO FUE SELECCIONADO PONEMOS POR DEFECTO INHERIT
                                    if(!$scope.tabs[i].fontFamily){
                                        $scope.tabs[i].fontFamily = "inherit";
                                    }
                                    //$scope.tabs[i].idcustomfield
                                    $scope.data.customFields.push({
                                            
                                            idCF : $scope.tabs[i].idcustomfield,
                                            fontSize: $scope.tabs[i].fontSize, 
                                            color: $scope.tabs[i].color, 
                                            fontWeight: $scope.tabs[i].fontWeight, 
                                            fontStyle: $scope.tabs[i].fontStyle,
                                            textDecoration: $scope.tabs[i].textDecoration,
                                            fontFamily: $scope.tabs[i].fontFamily
                                            
                                    });
                                }
                                $scope.data.textAlign = $scope.textAlign;
                                if(!$scope.resCP && $scope.idCPmixed == 0){
                                    $scope.data.idCFmix = "";
                                }
                                if(!$scope.resCP && $scope.idCPmixed != 0){
                                    $scope.data.idCFmix = $scope.idCPmixed;
                                }else{
                                    $scope.data.idCFmix = $scope.resCP.idCustomfield;
                                }    
                            }else{
                                notificationService.error("Para crear autrespuesta con opciones avanzadas debe seleccionar al menos 2 campos personalizados.");
                                return;
                            }
                            
                        }else{
                            $scope.data.customFields = null;
                        }
                        
                        $scope.data.addressees = $scope.addressees;
                        $scope.data.time = $("#valueDatepicker").val();

                        RestServices.createAutoresponder($scope.data, $stateParams.id).then(function (res) {
                            $window.location.href = fullUrlBase + $scope.data.autorespondercontent.url + res.autoresponder.idAutoresponder;
                        });
                    };

                    $scope.filters = [];
                    $scope.tipeFilters = [
                        {id: 1, name: "Enviar a contactos que hayan recibido un correo"},
                        {id: 2, name: "Enviar a contactos que hayan abierto un correo"},
                        {id: 3, name: "Enviar a contactos que hayan hecho clic un enlace"}
                    ];
                    $scope.selectMailFilter = function (key) {
                        if (key.typeFilters == 3) {
                            RestServices.getLinksByMail(key.mailSelected).then(function (data) {
                                key.links = data;
                            });
                        } else {
                            if (!$scope.addressees.showContactlist) {
                                $scope.countContacts("contactlist");
                            } else {
                                $scope.countContacts("segment");
                            }

                        }
                    };

                    $scope.selectLinkFilter = function () {
                        if (!$scope.addressees.showContactlist) {
                            $scope.countContacts("contactlist");
                        } else {
                            $scope.countContacts("segment");
                        }
                    };

                    $scope.selectTypeFilter = function (key) {
                        key.mailSelected = [];
                        key.linkSelected = [];
                        key.mail = [];
                        key.links = [];
                        RestServices.getMailFilters(key.typeFilters).then(function (data) {
                            key.mail = data;
                        });
                        switch (key.typeFilters) {
                            case 1:

                                break;
                            case 2:

                                break;
                            case 3:

                                break;
                        }
                    };

                    $scope.addFilter = function () {
                        $scope.addressees.condition = "all";
                        $scope.filters.push({});
                    };

                    $scope.removeFilters = function (index) {
                        $scope.filters.splice(index, 1);
                        if (!$scope.addressees.showContactlist) {
                            $scope.countContacts("contactlist");
                        } else {
                            $scope.countContacts("segment");
                        }
                    };

                }])
            .controller('birthdaySmsController', ['$q', '$scope', 'RestServices', 'notificationService', '$window', '$stateParams', function ($q, $scope, RestServices, notificationService, $window, $stateParams) {
                    $scope.data = {};
                    $scope.filter = "";
                    $scope.misc = {};
                    $scope.data.birthdate = false;
                    $scope.data.status = true;
                    $scope.data.class = "sms";
                    $scope.data.message = "";
                    //$scope.data.category = false;
                    $scope.data.idSmsCategory = "";
                    $scope.data.scheduledate = "";
                    $scope.initial = 0;
                    $scope.tags = "";
                    $scope.boolEditors = true;
                    $scope.data.morecaracter = false;
                    $scope.data.days = {
                        Monday: false,
                        Tuesday: false,
                        Wednesday: false,
                        Thursday: false,
                        Friday: false,
                        Saturday: false,
                        Sunday: false
                    };
                    $scope.misc.category = false;
                    $scope.addressees = [];
                    $scope.addressees.results = 0;

                    if ($stateParams.id) {
                        RestServices.getAutoresponder($stateParams.id).then(function (res) {
                            $scope.data = res.autoresponder;
                            $("#valueDatepicker").val(res.autoresponder.time);

                            $scope.data.status = (res.autoresponder.status == 1);

                            if (res.autoresponder.target) {
                                $scope.addressees.showstep1 = false;
                                var json = jQuery.parseJSON(res.autoresponder.target);
                                $scope.addressees.results = res.autoresponder.quantitytarget;
                                $scope.addressees.condition = json.condition;
                                $scope.filters = json.filters;
                                if (json.type == "contactlist") {
                                    $scope.addressees.showContactlist = false;
                                    $scope.addressees.selectdContactlis = json.contactlists;
                                    $scope.getAllContactlist();

                                } else if (json.type == "segment") {
                                    $scope.addressees.showSegment = false;
                                    $scope.addressees.selectdSegment = json.segment;
                                    $scope.getAllSegment();
                                }
                            }

                            if (!res.autoresponder.autorespondercontent) {
                                $scope.boolEditors = true;
                            } else {
                                $scope.boolEditors = false;
                                htmlPreview($stateParams.id);
                                $scope.getUrl = res.autoresponder.autorespondercontent.url;
                            }
                        });
                    }

                    $scope.addressees = {selectdContactlis: []};
                    $scope.addressees = {selectdSegment: []};
                    $scope.addressees.showSegment = true;
                    $scope.addressees.showstep1 = true;
                    $scope.addressees.showContactlist = true;

                    $scope.opeModalMoreCa = function () {
                        if ($('#morecaracter').prop('checked')) {
                            $('#alertMoreCaracter').removeClass('modal');
                            $('#alertMoreCaracter').addClass('dialog dialog--open');
                        }
                    }

                    $('#dateInitial').datetimepicker({
                        format: 'hh:mm',
                        language: 'es'
                    }).on('changeDate', function (ev) {
                        $scope.$apply();
                    });
                    $scope.birthdatefunction = function () {
                        if ($scope.data.birthdate) {
                            $scope.data.birthdate = false;
                            $scope.data.days = {}
                        } else {
                            $scope.data.birthdate = true;
                            $scope.data.days = {
                                Monday: true, Tuesday: true, Wednesday: true, Thursday: true,
                                Friday: true, Saturday: true, Sunday: true};
                        }
                    };
                    $scope.saveAutorespdesms = function () {
                        $scope.data.addressees = $scope.addressees;
                        $scope.data.time = $("#valueDatepicker").val();
                        $scope.data.filters = $scope.filters;
                        $scope.data.condition = $scope.addressees.condition;
                        var idAutoresponder = $stateParams.id ? $stateParams.id : 0;
                        RestServices.createAutorespdesms($scope.data, idAutoresponder).then(function (res) {

                            notificationService.success(res['message']);
                            $window.location.href = fullUrlBase + templateBase + "#/";
                        });
                    };
                    $scope.clearSelect = function () {
                        $scope.filters = [];
                        $scope.addressees.results = 0;
                        $scope.disabledContactlist = false;
                        $scope.disabledSegment = false;
                        $scope.addressees.selectdContactlis = "";
                        $scope.addressees.selectdSegment = "";
                    };
                    $scope.getContactlist = function () {
                        $scope.addressees = {selectdSegment: []};
                        $scope.segments = [];
                        $scope.filters = [];
                        $scope.contactlists = [];
                        $scope.addressees.showstep1 = false;
                        $scope.addressees.results = 0;
                        $scope.addressees.showSegment = true;
                        $scope.addressees.showContactlist = false;
                        $scope.prueba = undefined;
                        $scope.getAllContactlist();
                        $scope.getAllSegment();
                    };
                    $scope.setAllContactList = function () {

                    }
                    $scope.getAllContactlist = function () {
                        RestServices.getContactlist().then(function (data) {
                            $scope.contactlists = data;
                        });
                    };
                    $scope.getSegment = function () {
                        $scope.addressees = {selectdContactlis: []};
                        $scope.segments = [];
                        $scope.contactlists = [];
                        $scope.filters = [];
                        $scope.addressees.showstep1 = false;
                        $scope.addressees.count = 0;
                        $scope.prueba = undefined;
                        $scope.prueba2 = undefined;
                        $scope.addressees.selectdContactlis = [];
                        $scope.addressees.selectdSegment = [];
                        $scope.addressees.showSegment = false;
                        $scope.addressees.showContactlist = true;
                        $scope.getAllSegment();
                        $scope.getAllContactlist();
                    };
                    $scope.getAllSegment = function () {
                        RestServices.getSegment().then(function (data) {
                            $scope.segments = data;
                        });
                    };
                    $scope.selectAction = function () {
                        $scope.countContacts("contactlist");
                    };
                    $scope.selectActionSegment = function () {
                        $scope.countContacts("segment");
                    };
                    $scope.allSegment = function () {
                        $scope.disabledSegment = true;
                        $scope.addressees.selectdSegment = $scope.segments;
                        $scope.countContacts("segment");
                    };
                    $scope.allContactlist = function () {
                        $scope.disabledContactlist = true;
                        $scope.addressees.selectdContactlis = $scope.contactlists;
                        $scope.countContacts("contactlist");
                    };
                    $scope.countContacts = function (type) {
                        $scope.addressees.results = 0;
                        var data = {
                            type: type,
                            segment: $scope.addressees.selectdSegment,
                            contactlist: $scope.addressees.selectdContactlis,
//            filters: $scope.filters,
//            condition: $scope.addressees.condition,
//            idMail: $scope.idMail
                        };
                        RestServices.countContactFromSms(data).then(function (data) {
                            $scope.addressees.results = data;
                        });
                    };
                    $scope.validateInLine = function (type) {
                        $scope.misc.invalidCharacters = false;
                        $scope.misc.existTags = false;
                        $scope.misc.taggedMessage = $scope.data.message;
                        $scope.misc.newMessage = $scope.data.message;
                        var tags = /%%+[a-zA-Z0-9_]+%%/;
                        var count = 0;
                        if (tags.test($scope.data.message)) {
                            $scope.misc.existTags = true;
                            $scope.misc.taggedMessage = "";
                            $scope.misc.newMessage = "";

                            var words = $scope.data.message.split(" ");
                            for (var cont = 0; cont < words.length; cont++) {
                                var word = words[cont];
                                var word2 = words[cont];
                                if (word.substr(0, 2) == "%%" && (word.substr(-2) == "%%" || word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;")) {
                                    word = word.substr(2);
                                    word2 = "";
                                    word = "<b><i>" + word;
                                    if (word.substr(-2) == "%%") {
                                        word = word.substr(0, word.length - 2);
                                    } else if (word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;") {
                                        word = word.substr(0, word.length - 3);
                                    }
                                    word = word + "</i></b>";
                                    count = count + word.length;
                                }
                                $scope.misc.taggedMessage += word + " ";
                                $scope.misc.newMessage += word2 + " ";
                            }
                        }
                    };

                    $scope.addTag = function (tag) {
                        if (typeof $scope.data.message == "undefined") {
                            $scope.data.message = "";
                            $scope.data.message += tag;
                        } else {
                            $scope.data.message += " " + tag;
                        }
                    };
                    $scope.setTargetAuto = function (prm) {
                        if (prm.type == "contactlist") {
                            $scope.addressees.selectdContactlis = prm.contactlists;
                        } else if (prm.type == "segment") {
                            $scope.addressees.selectdSegment = prm.selectdSegment;
                        }
                        $scope.countContacts(prm.type);
                    };
                    $scope.setScheduleDate = function (prm) {
                        $scope.data.scheduledate = prm
                    };
                    $scope.setMessage = function (prm) {
                        $scope.data.message = prm
                    };
                    $scope.setIdSmsCategory = function (prm) {
                        $scope.data.idSmsCategory = prm;
                    };
                    $scope.setDays = function (prm) {
                        $scope.data.days = prm;
                    }
                    $scope.containsWord = function (haystack, needle) {
                        return (" " + haystack + " ").indexOf(" " + needle + " ") !== -1;
                    }
                    $scope.setSwitchBirthday = function (prm) {
                        $scope.data.birthdate = true;
                        if (prm) {
                            $('.onoffswitch-inner').click();
                        }
                    }
                    $scope.getDataAll = function () {
                        if ($stateParams.id) {
                            RestServices.getDataAll($stateParams.id).then(function (data) {
                                //$scope.setIdSmsCategory();
                                $scope.setTargetAuto(data.target);
                                $scope.setScheduleDate(data.scheduledate);
                                $scope.setSwitchBirthday(data.birthdate);
                                $scope.setDays(data.days);
                                $scope.setMessage(data.message);
                                if (data.morecaracter === true) {
                                    $("#morecaracter").prop('checked', true);
                                } else {
                                    $("#morecaracter").prop('checked', false);
                                }
                            }).catch(function (data) {
                            })
                        }
                    };

                    $scope.getAllSmsCategories = function () {
                        RestServices.getAllSmsCategories().then(function (res) {
                            $scope.misc.category = res;
                        });
                    };
                    $scope.getAllSmsCategories();

                    var arrInitialPeticion = [$scope.getDataAll()];
                    $q.all(arrInitialPeticion).then(function (data) {
                    }).catch(function (error) {})

                }])
            .controller('contentEditorAutoresController', ['$scope', 'RestServices', 'notificationService', '$window', '$stateParams', function ($scope, RestServices, notificationService, $window, $stateParams) {
                    $scope.saveContentEditor = function () {
                        var data = {
                            editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
                        };
                        RestServices.createContentEditorAutoresponder(data, idAutoresponder).then(function (res) {
                            if (res.operation == "create") {
                                notificationService.success(res.message);
                            } else if (res.operation == "edit") {
                                notificationService.primary(res.message);
                            }
                            $window.location.href = fullUrlBase + templateBase + '#/birthday/' + res.autoresponderContent.idAutoresponder;
                        });
                    }
                }])
            .controller('toolsController', ['$scope', 'RestServices', 'notificationService', '$window', '$stateParams', function ($scope, RestServices, notificationService, $window, $stateParams) {}])
})();
