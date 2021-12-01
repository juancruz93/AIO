var socket = io.connect('http://localhost:3000/', {'forceNew': true});
//var socket = io.connect('https://wstest.sigmamovil.com/', {'forceNew': true});
socket.on('messages', function(data) {
    console.log(data);
    render(data);
});

socket.on('respuesta', function(data) {
    console.log(data);
    renderRespuesta(data);
});

function sentData(){
  alert("hola");
  socket.emit('refresh-view-sms', "hola");
}

function render(data) {
    var html = data.map(function(elem, index){
        return(`<div>
                 <strong>${elem.idSms}</strong>
        </div>`)
    }).join(" ");

    document.getElementById('messages').innerHTML = html;
}

function renderRespuesta(data) {
    var html = `<div><strong>${data}</strong></div>`;

    document.getElementById('respuesta').innerHTML = html;
}

function addMessage(e) {
    var mensaje = {
        idSms: document.getElementById('idSms').value,
        //text: document.getElementById('texto').value
    };

    socket.emit('send-message', mensaje);
    return false;
}
