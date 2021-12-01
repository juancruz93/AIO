//var socket = io.connect('https://testtrack.sigmamovil.com/', {'forceNew': true});
//var socket = io.connect('http://localhost:3000/', {'forceNew': true});
var socket = io.connect('https://ws.sigmamovil.com/', {'forceNew': true});
//var socket = io.connect('https://wstest.sigmamovil.com/', {'forceNew': true});//test
socket.on('messages', function (data) {
//    console.log(data);
    //render(data);
});
     
socket.on('respuesta', function (data) {
    console.log(data);
    //renderRespuesta(data);
});

function render(data) {
    var html = data.map(function (elem, index) {
        return (`<div>
                 <strong>${elem.idSms}</strong>
        </div>`)
    }).join(" ");

    document.getElementById('messages').innerHTML = html;
}

function renderRespuesta(data) {
    var html = `<div><strong>${data}</strong></div>`;

    document.getElementById('respuesta').innerHTML = html;
}

function pauseSms(id) {
    var data = {
        idSms: id,
        nameFunc: "pause"
    };
    //console.log(data);
    socket.emit('pause-send-sms', data);
    slideOnTop("El envio de SMS se ha pausado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
    setTimeout(function () {
        location.reload();
    }, 3000);
    return false;
}
var idSms = "";
function loadIdSms(id){
   idSms = id.toString();
}
function cancelSms() {
    let id = idSms.toString();
 
    var data = {
        idSms: id,
        nameFunc: "cancel"
    };
    //console.log(data);
    socket.emit('cancel-send-sms', data);
//    slideOnTop("El envio de sms se ha cancelado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
//    setTimeout(function () {
//        location.reload();
//    }, 3000);
    slideOnTop("El envio de SMS se ha cancelado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
    setTimeout(function () {
        location.reload();
    }, 3000);
    return false;
}

function resumeSms(id) {
    var data = {
        idSms: id
    };
    //console.log(data);
    socket.emit('restart-send-sms', data);
    slideOnTop("El envio se ha reanudado correctamente", 3000, "glyphicon glyphicon-ok", "success");
    setTimeout(function () {
        location.reload();
    }, 5000);
    return false;
}

function pauseMail(id) {
    var data = {
        idMail: id,
        nameFunc: "pause"
    };
    //console.log(data);
    socket.emit('pause-send-mail', data);
    slideOnTop("El envio de mail se ha pausado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
    return false;
}

function cancelMail(id) {
    var data = {
        idMail: id,
        nameFunc: "cancel"
    };
    //console.log(data);
    socket.emit('cancel-send-mail', data);
    slideOnTop("El envio de mail se ha cancelado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
    return false;
}

function resumeMail(id) {
    var data = {
        idMail: id
    };
    //console.log(data);
    socket.emit('restart-send-mail', data);
    slideOnTop("El envio de mail se ha reanudado correctamente", 3000, "glyphicon glyphicon-ok", "success");
    return false;
}
