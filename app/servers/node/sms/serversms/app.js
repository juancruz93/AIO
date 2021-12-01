var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');

var routes = require('./routes/index');
var users = require('./routes/users');

var mysql = require('mysql'),
// Crea la conexion a la base de datos
//        connection = mysql.createConnection({
//          host: '192.168.18.13',
//          port: 3306,
//          user: 'root',
//          password: '',
//          database: 'aio'
//        }),
        //server
  connection = mysql.createConnection({
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: 'KT3a6!h&GV_h33X',
    database: 'aio'
  }),
// Array que guarda los usuarios conectados
        connectionsArray = [],
// Intervalo de tiempo con el que se estaran haciendo peticiones a la base de datos
        POLLING_INTERVAL = 1000,
        pollingTimer,
// Variable que cuenta los procesos hijos de SMS en ejecucion
  countProcessSms = 0,
  countProcessSmsTwoWay = 0,
  countProcessSmsContact = 0,
  countProcessMailStatisticNotification = 0,
  countProcessMail = 0,
  countProcessCampaign = 0,
  countProcessCampaignStep = 0,
  countProcessAutoresponder = 0,
  countProcessSmsAutoresponder = 0,
  countProcessImport = 0;

var app = express();

// call socket.io to the app
var io = require('socket.io');
app.io = io();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: false}));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', routes);
app.use('/users', users);

// catch 404 and forward to error handler
app.use(function (req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function (err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function (err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});

var messages = [{
    idSms: "4"
  }];

var spawn = require('child_process');

var sms = [],
  smsContact = [],
  MailStatisticNotification = [],
  pidProcessRunSms = [],
  pidProcessRunSmsContact = [],
  mail = [],
  campaign = [],
  campaignStep = [],
  autoresponder = [],
  autorespondersms = [],
  smstwoway = [],
  pidProcessRunMail = [],
  pidProcessRunCampaign = [],
  pidProcessRunCampaignStep = [],
  pidProcessRunMailStatisticNotification = [],
  pidProcessRunAutoresponder = [],
  pidProcessRunSmsAutoresponder = [],
  pidProcessRunSmsTwoWay = [],
  pidProcessRunImport = []; // this array will contain the result of our db query

var fs = require('fs');

function log(msg) {
  fs.appendFile(path.join(__dirname, '../../../../logs/serverNode.log'), msg + '\r\n', {'flag': 'a'}, function (err) {
    if (err)
      throw err;
    console.log('Se ha registrado un log');
  });
}

var pollingLoopSms = function () {
  sms = [];
  // Make the database query
  var query = connection.query('SELECT * FROM viewsms');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (sms.length == 0) {
      sms.push(row);
    } else {
      if (!searchIdSms(row.idSms)) {
        sms.push(row);
      }
    }
    //console.log(sms);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopSms, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });
};

var pollingLoopSmsTwoWay = function () {
  smstwoway = [];
  var query = connection.query('SELECT * FROM viewsmstwoway');

  query.on('error', function (err) {
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    console.log(row);

    if (smstwoway.length == 0) {
      smstwoway.push(row);
    } else {
      if (!searchIdSmsTwoWay(row.idSmsTwoway)) {
        smstwoway.push(row);
      }
    }
    //console.log(sms);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopSmsTwoWay, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });
};

/*Funcion que se encarga de validar si un sms ya existe en el array*/
function searchIdSms(idSms) {
  for (var i = 0; i < sms.length; i++) {
    if (idSms == sms[i].idSms) {
      return true;
    }
  }
  return false;
}
/*Funcion que se encarga de validar si un smstwoway ya existe en el array*/
function searchIdSmsTwoWay(idSmsTwoWay) {
  for (var i = 0; i < smstwoway.length; i++) {
    if (idSmsTwoWay == smstwoway[i].idSmsTwoWay) {
      return true;
    }
  }
  return false;
}

var pollingLoopSmsContact = function () {
  smsContact = [];
  // Make the database query
  var query = connection.query('SELECT * FROM viewsmscontact');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (smsContact.length == 0) {
      smsContact.push(row);
    } else {
      if (!searchIdSmsContact(row.idSms)) {
        smsContact.push(row);
      }
    }
    //console.log(sms);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopSmsContact, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Funcion que se encarga de validar si un sms ya existe en el array*/
function searchIdSmsContact(idSms) {
  for (var i = 0; i < smsContact.length; i++) {
    if (idSms == smsContact[i].idSms) {
      return true;
    }
  }
  return false;
}

var pollingLoopMailStatisticNotification = function () {
  MailStatisticNotification = [];
  // Make the database query
  var query = connection.query('SELECT * FROM view_statistic_mail_sender');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (MailStatisticNotification.length == 0) {
      MailStatisticNotification.push(row);
    } else {
      if (!searchIdMailStatisticNotification(row.idMailStatisticNotification)) {
        MailStatisticNotification.push(row);
      }
    }
    //console.log(sms);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopMailStatisticNotification, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Funcion que se encarga de validar si un sms ya existe en el array*/
function searchIdMailStatisticNotification(idMailStatisticNotification) {
  for (var i = 0; i < MailStatisticNotification.length; i++) {
    if (idMailStatisticNotification == MailStatisticNotification[i].idMailStatisticNotification) {
      return true;
    }
  }
  return false;
}

var pollingLoopMail = function () {
  mail = [];
  // Make the database query
  var query = connection.query('SELECT * FROM mail_sheduled');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (mail.length == 0) {
      mail.push(row);
    } else {
      if (!searchIdMail(row.idMail)) {
        mail.push(row);
      }
    }
    //console.log(row.idMail);
    //console.log(mail);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopMail, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Funcion que se encarga de validar si un mail ya existe en el array*/
function searchIdMail(idMail) {
  for (var i = 0; i < mail.length; i++) {
    if (idMail == mail[i].idMail) {
      return true;
    }
  }
  return false;
}

/*Funcion que se encarga de consultar si hay Mail en estado de Sending */
var pollingLoopMailSending = function () {
  // Se realiza la validacion de que la campaña No haya cambiado de estado correctamente 
  var query = connection.query('SELECT * FROM mail_sending');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if(searchPidMailProcess(row.idMail) == undefined) {
      console.log("pollingLoopMailSending");
      spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/mail/MailPendingStatus.php ') + row.idMail);
    }
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopMailSending, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });
};

/*Funcion que se encarga de consultar si hay registros en la vista start_automatic_campaign */
var pollingLoopAutomaticCampaign = function () {
  campaign = [];
  // Make the database query
  var query = connection.query('SELECT * FROM start_automatic_campaign');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (campaign.length == 0) {
      campaign.push(row);
    } else {
      if (!searchCampaign(row.idAutomaticCampaign)) {
        campaign.push(row);
      }
    }
    //console.log(row.idMail);
    //console.log(mail);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopAutomaticCampaign, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Consultar si la campaña ya existe en el array*/
var searchCampaign = function (idCampaign) {
  for (var i = 0; i < campaign.length; i++) {
    if (idCampaign == campaign[i].idAutomaticCampaign) {
      return true;
    }
  }
  return false;
}

/*Funcion que se encarga de consultar si hay registros en la vista start_automatic_campaign */
var pollingLoopAutomaticCampaignStep = function () {
  campaignStep = [];
  // Make the database query
  var query = connection.query('SELECT * FROM next_step');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (campaignStep.length == 0) {
      campaignStep.push(row);
    } else {
      if (!searchCampaignStep(row.idAutomaticCampaignStep)) {
        campaignStep.push(row);
      }
    }
    //console.log(row.idMail);
    //console.log(mail);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopAutomaticCampaignStep, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Consultar si la campaña ya existe en el array*/
var searchCampaignStep = function (idCampaignStep) {
  for (var i = 0; i < campaignStep.length; i++) {
    if (idCampaignStep == campaignStep[i].idAutomaticCampaignStep) {
      return true;
    }
  }
  return false;
}

var pollingLoopAutoresponder = function () {
  autoresponder = [];
  // Make the database query
  var query = connection.query('SELECT * FROM view_autoresponder WHERE class = "mail"');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (autoresponder.length == 0) {
      autoresponder.push(row);
    } else {
      if (!searchIdAutoresponder(row.idAutoresponder)) {
        autoresponder.push(row);
      }
    }
    //console.log(row.idMail);
    //console.log(mail);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopAutoresponder, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};
var pollingLoopSmsAutoresponder = function () {
  autorespondersms = [];
  // Make the database query
  var query = connection.query('SELECT * FROM view_autoresponder WHERE class = "sms"');

  // set up the query listeners
  query.on('error', function (err) {
    // Handle error, and 'end' event will be emitted after this as well
    log(err);
    updateSockets(err);

  }).on('result', function (row) {
    // it fills our array looping on each user row inside the db
    if (autorespondersms.length == 0) {
      autorespondersms.push(row);
    } else {
      if (!searchIdSmsAutoresponder(row.idAutoresponder)) {
        autorespondersms.push(row);
      }
    }
    //console.log(row.idMail);
    //console.log(mail);
  }).on('end', function () {
    // loop on itself only if there are sockets still connected
    if (connectionsArray.length) {
      pollingTimer = setTimeout(pollingLoopSmsAutoresponder, POLLING_INTERVAL);
      //updateSockets({sms: sms});
    }
  });

};

/*Funcion que se encarga de validar si una auto respuesta que ya existe en el array*/
function searchIdAutoresponder(idAutoresponder) {
  for (var i = 0; i < autoresponder.length; i++) {
    if (idAutoresponder == autoresponder[i].idAutoresponder) {
      return true;
    }
  }
  return false;
}
;

/*Funcion que se encarga de validar si una auto respuesta de sms que ya existe en el array*/
function searchIdSmsAutoresponder(idAutoresponder) {
  for (var i = 0; i < autorespondersms.length; i++) {
    if (idAutoresponder == autorespondersms[i].idAutoresponder) {
      return true;
    }
  }
  return false;
}
;

var importStart = function (data) {

  countProcessImport++;
  if (countProcessImport <= 20) {

    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/contacts/Import.php ') + data.import.idImportcontactfile, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });

    var dataImport = {
      idImportcontactfile: data.import.idImportcontactfile,
      pid: childProc.pid,
      rows: data.import.rows,
      processed: data.import.processed
    };
    pidProcessRunImport.push(dataImport);

    childProc.on('close', function (code, signal) {
      countProcessImport--;
      pidProcessRunImport.forEach(deletePidTerminateImport.bind({id: data.import.idImportcontactfile}));
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessImport--;
  }
};

var stopServerNode = function () {

  var childProc = spawn.exec('forever stopall', function (error, stdout, stderr) {
    if (error) {
      log('err: ' + error.stack);
      log('Error code: ' + error.code);
      log('Signal received: ' + error.signal);
    }
    log('Child Process STDOUT: ' + stdout);
    log('Child Process STDERR: ' + stderr);
    app.io.sockets.emit('respuesta', stdout);
  });

  childProc.on('close', function (code, signal) {
    log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
  });
};

var restartServerNode = function () {

  var childProc = spawn.exec('forever restartall', function (error, stdout, stderr) {
    if (error) {
      log('err: ' + error.stack);
      log('Error code: ' + error.code);
      log('Signal received: ' + error.signal);
    }
    log('Child Process STDOUT: ' + stdout);
    log('Child Process STDERR: ' + stderr);
    app.io.sockets.emit('respuesta', stdout);
  });

  childProc.on('close', function (code, signal) {
    log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
  });
};

var updateSockets = function (data) {
  // store the time of the latest update
  data.time = new Date();
  // send new data to all the sockets connected
  connectionsArray.forEach(function (tmpSocket) {
    tmpSocket.volatile.emit('respuesta', data);
  });
};
// Funcion que se encarga de verificar si hay envios de SMS para realizar
var verifySendSms = function () {
  if (sms.length == 0) {
    console.log('Sin Mensajes para enviar');
  } else {
    sms.forEach(createChildProcessSms);
  }
};

var verifySendSmsTwoWay = function () {
  if (smstwoway.length == 0) {
    console.log('Sin Mensajes para enviar');
  } else {
    smstwoway.forEach(createChildProcessSmsTwoWay);
  }
};

// Funcion que se encarga de verificar si hay envios de SMS para realizar
var verifySendSmsContact = function () {
  if (smsContact.length == 0) {
    console.log('Sin Mensajes para enviar');
  } else {
    smsContact.forEach(createChildProcessSmsContact);
  }
};

// Funcion que se encarga de verificar si hay envios Estadísticas de Mail para realizar
var verifySendMailStatisticNotification = function () {
  if (MailStatisticNotification.length == 0) {
    console.log('Sin Mensajes para enviar');
  } else {
    MailStatisticNotification.forEach(createChildProcessMailStatisticNotification);
  }
};
// Funcion que se encarga de verificar si hay envios de MAIL para realizar
var verifySendMail = function () {
//console.log(mail);
  if (mail.length == 0) {
    console.log('Sin Mails para enviar');
  } else {
    mail.forEach(createChildProcessMail);
  }
};

// Funcion que se encarga de verificar si hay envios de CAMPAING para realizar
var verifySendCampaign = function () {
//console.log(mail);
  if (campaign.length == 0) {
    console.log('Sin Campaigns para enviar');
  } else {
    campaign.forEach(createChildProcessCampaign);
  }
};

// Funcion que se encarga de verificar si hay envios de CAMPAING para realizar
var verifySendCampaignStep = function () {
//console.log(mail);
  if (campaignStep.length == 0) {
    console.log('Sin Step para enviar');
  } else {
    campaignStep.forEach(createChildProcessCampaignStep);
  }
};

// Funcion que se encarga de verificar si hay AUTORESPUESTAS para realizar
var verifySendAutoresponder = function () {
  if (autoresponder.length == 0) {
    console.log('Sin Autorespuestas para enviar');
  } else {
    autoresponder.forEach(createChildProcessAutoresponder);
  }
};

// Funcion que se encarga de verificar si hay AUTORESPUESTAS de SMS para realizar
var verifySendSmsAutoresponder = function () {
  if (autorespondersms.length == 0) {
    console.log('Sin Autorespuestas de Sms para enviar');
  } else {
    autorespondersms.forEach(createChildProcessSmsAutoresponder);
  }
};

// Funcion que se encarga de crear los subprocesos de SMS
function createChildProcessSms(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessSms++;
  if (countProcessSms <= 20) {
    sms.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/sms/SmsSender.php ') + element.idSms, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idSms: element.idSms,
      pid: childProc.pid
    };
    pidProcessRunSms.push(data);
    childProc.on('close', function (code, signal) {
      countProcessSms--;
      pidProcessRunSms.forEach(deletePidTerminate.bind({id: element.idSms}));
      log("idsms: " + element.idSms);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessSms--;
  }
}

// Funcion que se encarga de crear los subprocesos de SMS
function createChildProcessSmsTwoWay(element, index, array) {
  console.log(element);
  countProcessSmsTwoWay++;
  if (countProcessSmsTwoWay <= 20) {
    smstwoway.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/smstwoway/SmsSender.php ') + element.idSmsTwoway, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idSmsTwoway: element.idSmsTwoway,
      pid: childProc.pid
    };
    pidProcessRunSmsTwoWay.push(data);
    childProc.on('close', function (code, signal) {
      countProcessSmsTwoWay--;
      pidProcessRunSmsTwoWay.forEach(deletePidTerminateTwoWay.bind({id: element.idSmsTwoway}));
      log("createChildProcessSmsTwoWay: " + element.idSmsTwoWay);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessSmsTwoWay--;
  }
}

// Funcion que se encarga de crear los subprocesos de SMS
function createChildProcessSmsContact(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessSmsContact++;
  if (countProcessSmsContact <= 20) {
    smsContact.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/sms/SmsSenderContact.php ') + element.idSms, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idSms: element.idSms,
      pid: childProc.pid
    };
    pidProcessRunSmsContact.push(data);
    childProc.on('close', function (code, signal) {
      countProcessSmsContact--;
      pidProcessRunSmsContact.forEach(deletePidTerminateSmsContact.bind({id: element.idSms}));
      log("idsms: " + element.idSms);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
      
    });
  } else {
    countProcessSmsContact--;
  }
}

// Funcion que se encarga de crear los subprocesos de MailStatisticNotification
function createChildProcessMailStatisticNotification(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessMailStatisticNotification++;
  if (countProcessMailStatisticNotification <= 20) {
    MailStatisticNotification.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/statistic/SenderStatisticMail.php ') + element.idMailStatisticNotification, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idMailStatisticNotification: element.idMailStatisticNotification,
      pid: childProc.pid
    };
    pidProcessRunMailStatisticNotification.push(data);
    childProc.on('close', function (code, signal) {
      countProcessMailStatisticNotification--;
      pidProcessRunMailStatisticNotification.forEach(deletePidTerminateMailStatisticNotification.bind({id: element.idMailStatisticNotification}));
      log("idMailStatisticNotification: " + element.idMailStatisticNotification);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessMailStatisticNotification--;
  }
}

// Funcion que se encarga de crear los subprocesos de EMAIL
function createChildProcessMail(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessMail++;
  //Se realiza la busqueda en los procesos que se estan ejecutando el idMail.
  //const pid = pidProcessRunMail.find(mail => mail.idMail === element.idMail);
  //El resultado debe ser undefine siempre si no lo es quiere decir que hay registros.
  if (countProcessMail <= 20 && searchPidMailProcess(element.idMail) == undefined) {
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/mail/MailSender.php ') + element.idMail, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idMail: element.idMail,
      pid: childProc.pid
    };
    pidProcessRunMail.push(data);
    childProc.on('close', function (code, signal) {
      countProcessMail--;
      pidProcessRunMail.forEach(deletePidTerminateMail.bind({id: element.idMail}));
      log("idMail: " + element.idMail);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
    mail.splice(index, 1);
  } else {
    countProcessMail--;
  }
}

// Funcion que se encarga de crear los subprocesos de EMAIL
function createChildProcessCampaign(element, index, array) {
//  console.log("a[" + index + "] = "+element);
//  log('Child Process STDOUT: ' + element);
//  return;
  countProcessCampaign++;
  if (countProcessCampaign <= 20 && searchPidAutomaticProcess(element.idAutomaticCampaign) == undefined) {
    campaign.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/campaign/StartAutomaticCampaignSender.php ') + element.idAutomaticCampaign, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idAutomaticCampaign: element.idAutomaticCampaign,
      pid: childProc.pid,
      name: element.name,
      status: element.status
    };
    pidProcessRunCampaign.push(data);
    childProc.on('close', function (code, signal) {
      countProcessCampaign--;
      pidProcessRunCampaign.forEach(deletePidTerminateCampaign.bind({id: element.idAutomaticCampaign}));
      log("idAutomaticCampaign: " + element.idAutomaticCampaign);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessCampaign--;
  }
}

// Funcion que se encarga de crear los subprocesos de EMAIL
function createChildProcessCampaignStep(element, index, array) {
//  console.log("a[" + index + "] = "+element);
//  log('Child Process STDOUT: ' + element);
//  return;
  countProcessCampaignStep++;
  if (countProcessCampaignStep <= 20 && searchPidAutomaticStepProcess(element.idAutomaticCampaignStep) == undefined) {
    campaignStep.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/campaign/SenderStepAutomatic.php ') + element.idAutomaticCampaignStep, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idAutomaticCampaignStep: element.idAutomaticCampaignStep,
      pid: childProc.pid,
      idContact: element.idContact,
      status: element.status,
      statusSms: element.statusSms
    };
    pidProcessRunCampaignStep.push(data);
    childProc.on('close', function (code, signal) {
      countProcessCampaignStep--;
      pidProcessRunCampaignStep.forEach(deletePidTerminateCampaignStep.bind({id: element.idAutomaticCampaignStep}));
      log("idAutomaticCampaign: " + element.idAutomaticCampaign);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessCampaignStep--;
  }
}

// Funcion que se encarga de crear los subprocesos de AUTORESPUESTA
function createChildProcessAutoresponder(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessAutoresponder++;
  if (countProcessAutoresponder <= 20) {
    autoresponder.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/autoresponder/AutoresponderSender.php ') + element.idAutoresponder, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idAutoresponder: element.idAutoresponder,
      pid: childProc.pid,
      name: element.name
    };
    pidProcessRunAutoresponder.push(data);
    childProc.on('close', function (code, signal) {
      countProcessAutoresponder--;
      pidProcessRunAutoresponder.forEach(deletePidTerminateAutoresponder.bind({id: element.idAutoresponder}));
      log("idAutoresponder: " + element.idAutoresponder);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessAutoresponder--;
  }
}

// Funcion que se encarga de crear los subprocesos de AUTORESPUESTA POR SMS
function createChildProcessSmsAutoresponder(element, index, array) {
  //console.log("a[" + index + "] = " + element.idSms + "name: "+ element.name +" fecha: " + element.startdate);
  countProcessSmsAutoresponder++;
  if (countProcessSmsAutoresponder <= 20) {
    autorespondersms.splice(index, 1);
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/autoresponder/AutoresponderSmsSender.php ') + element.idAutoresponder, function (error, stdout, stderr) {
      //console.log(childProc);
//      console.log("este es el -error: " +  error);
//      console.log("este es el stdout: " + stdout);
//      console.log("este es el stderr: " + stderr);
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      log('Child Process STDERR: ' + stderr);
      app.io.sockets.emit('respuesta', stdout);
    });
    var data = {
      idAutoresponder: element.idAutoresponder,
      pid: childProc.pid,
      name: element.name
    };
    //console.log(data);
    pidProcessRunSmsAutoresponder.push(data);
    childProc.on('close', function (code, signal) {
      countProcessSmsAutoresponder--;
      pidProcessRunSmsAutoresponder.forEach(deletePidTerminateAutoresponder.bind({id: element.idAutoresponder}));
      log("idAutoresponder: " + element.idAutoresponder);
      log('Child process cerrado con codigo de salida: ' + code + ' signal: ' + signal);
    });
  } else {
    countProcessSmsAutoresponder--;
  }
}

// Funcion que se encarga de eliminar un pid de SMS que ya no esta en uso
function deletePidTerminate(element, index) {
  if (element.idSms == this.id) {
    pidProcessRunSms.splice(index, 1);
  }
}

function deletePidTerminateTwoWay(element, index) {
  if (element.idSmsTwoWay == this.id) {
    pidProcessRunSmsTwoWay.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de SMS que ya no esta en uso
function deletePidTerminateSmsContact(element, index) {
  if (element.idSms == this.id) {
    pidProcessRunSmsContact.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de MailStatisticNotification que ya no esta en uso
function deletePidTerminateMailStatisticNotification(element, index) {
  if (element.idMailStatisticNotification == this.id) {
    pidProcessRunMailStatisticNotification.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de MAIL que ya no esta en uso
function deletePidTerminateMail(element, index) {
  if (element.idMail == this.id) {
    pidProcessRunMail.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de AUTORESPUESTA que ya no esta en uso
function deletePidTerminateAutoresponder(element, index) {
  if (element.idAutoresponder == this.id) {
    pidProcessRunAutoresponder.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de Campaign que ya no esta en uso
function deletePidTerminateCampaign(element, index) {
  if (element.idAutomaticCampaign == this.id) {
    pidProcessRunCampaign.splice(index, 1);
  }
}

// Funcion que se encarga de eliminar un pid de Campaign que ya no esta en uso
function deletePidTerminateCampaignStep(element, index) {
  if (element.idAutomaticCampaignStep == this.id) {
    pidProcessRunCampaignStep.splice(index, 1);
  }
}


function deletePidTerminateImport(element, index) {
  if (element.idImportcontactfile == this.id) {
    pidProcessRunImport.splice(index, 1);
  }
}

// Funcion que se encarga de cambiar el estado a programado al SMS para ser reanudado 
var restartSendSms = function (data) {
  connection.query("UPDATE sms SET status='scheduled' WHERE idSms = " + data.idSms, function (err, rows) {
    if (err)
      log(err);
  });
};

// Funcion que se encarga de cambiar el estado a programado al MAIL para ser reanudado 
var restartSendMail = function (data) {
  connection.query("UPDATE mail SET status='scheduled' WHERE idMail = " + data.idMail, function (err, rows) {
    if (err)
      log(err);
  });
};

// Funcion que se encarga de cambiar el estado a programado al Campaign para ser reanudado 
var restartCampaign = function (data) {
  connection.query("UPDATE mail SET status='confirmed' WHERE idMail = " + data.idAutomaticCampaign, function (err, rows) {
    if (err)
      log(err);
  });
};
//Cancelar o pausar un mensaje de sms a nivel de base de datos
var stopSmsSchedule = function (data) {

  var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/ChangeStatusSms.php ') + data.idSms + ' ' + data.nameFunc, function (error, stdout, stderr) {
    if (error) {
      log('err: ' + error.stack);
      log('Error code: ' + error.code);
      log('Signal received: ' + error.signal);
    }
    log('Child Process STDOUT: ' + stdout);
    log('Child Process STDERR: ' + stderr);
//    app.io.sockets.emit('respuesta', stdout);
  });



//
//  //console.log(data);
//  if (data.nameFunc == "pause") {
//    v
//    connection.query("UPDATE sms SET status = 'paused' WHERE idSms = " + data.idSms, function (err, rows) {
//      if (err) {
//        log(err);
//      } else {
//        CountSentSms(data.idSms);
//      }
//    });
//  } else {
//    connection.query("UPDATE sms SET status = 'canceled' WHERE idSms = " + data.idSms, function (err, rows) {
//      if (err) {
//        log(err);
//      } else {
//        CountSentSms(data.idSms);
//      }
//    });
//  }
}

// Funcion que se encarga de detener un subproceso de SMS en ejecucion
var stopSendSms = function (data) {

  if (pidProcessRunSms.length == 0) {
    stopSmsSchedule(data);
  } else {
    var flagSmsSchedule = false;
    //Validar si existe un proceso con ese id de sms
    for (i in pidProcessRunSms) {
      if (data.idSms == pidProcessRunSms[i].idSms) {
        flagSmsSchedule = true;
        break;
      }
    }

    if (!flagSmsSchedule) {
      stopSmsSchedule(data);
    } else {
      pidProcessRunSms.forEach(stopChildProcessSms.bind(
              {data: data}
      )
              );
    }

  }
};

//Funcion para contar los sms enviados hasta que se 'Pauso' o se 'Cancelo'
function ChangeStatusSms(idSms) {
  var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/CountSentSms.php ') + idSms, function (error, stdout, stderr) {
    if (error) {
      log('err[CountSentSms]: ' + error.stack);
      log('Error code[CountSentSms]: ' + error.code);
      log('Signal received[CountSentSms]: ' + error.signal);
    }
    log('Child Process STDOUT[CountSentSms]: ' + stdout);
    log('Child Process STDERR[CountSentSms]: ' + stderr);
    app.io.sockets.emit('respuesta', stdout);
  });
}

// Funcion que se encarga de detener un subproceso de MAIL en ejecucion
var stopSendMail = function (data) {
  if (pidProcessRunMail.length == 0) {
    log('Sin Mails para cancelar');
  } else {
    pidProcessRunMail.forEach(stopChildProcessMail.bind({data: data}));
  }
};

// Funcion que se encarga de detener un subproceso de Campaign en ejecucion
var stopAutomaticCampaign = function (data) {
  if (pidProcessRunCampaign.length == 0) {
    console.log('Sin Campaign para cancelar');
  } else {
    pidProcessRunCampaign.forEach(stopChildProcessCampaign.bind({data: data}));
  }
};

//Funcion para contar los sms enviados hasta que se 'Pauso' o se 'Cancelo'
function CountSentSms(idSms) {
  var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/CountSentSms.php ') + idSms, function (error, stdout, stderr) {
    if (error) {
      log('err[CountSentSms]: ' + error.stack);
      log('Error code[CountSentSms]: ' + error.code);
      log('Signal received[CountSentSms]: ' + error.signal);
    }
    log('Child Process STDOUT[CountSentSms]: ' + stdout);
    log('Child Process STDERR[CountSentSms]: ' + stderr);
    app.io.sockets.emit('respuesta', stdout);
  });
}

//Funcion del foreach que pertenece al array pidProcessRunSms encargada de detener un proceso de SMS en especifico
function stopChildProcessSms(element, index) {
//  console.log(element);
//  console.log("index: " + index + "idSms: " + element.idSms + " pid " + element.pid + "numerito: " + this.data.id);
  var nameFunc = this.data.nameFunc;
  var idSms = element.idSms
  if (element.idSms == this.data.idSms) {
    pidProcessRunSms.splice(index, 1);
    var prue = spawn.exec('TASKKILL /PID ' + element.pid + ' /F /T', function (error, stdout, stderr) {
      console.log(error);
      console.log(stdout);
      console.log(stderr);
      //var prue = spawn.exec('kill ' + element.pid, function (error, stdout, stderr) {

      if (nameFunc == "pause") {
        connection.query("UPDATE sms SET status = 'paused' WHERE idSms = " + element.idSms, function (err, rows) {
          if (err) {
            log(err);
          } else {
            CountSentSms(element.idSms);
          }
        });
      } else {
        connection.query("UPDATE sms SET status = 'canceled' WHERE idSms = " + element.idSms, function (err, rows) {
          if (err) {
            log(err);
          } else {
            CountSentSms(element.idSms);
          }
        });
      }

      if (error) {
        log('err: ' + error.stack);
        log('otro Error code: ' + error.code);
        log('otro Signal received: ' + error.signal);
      }
      log('otro Child Process STDOUT: ' + stdout);
      log('otro Child Process STDERR: ' + stderr);
    });
  }
}

//Funcion del foreach que pertenece al array pidProcessRunMail encargada de detener un proceso de MAIL en especifico
function stopChildProcessMail(element, index) {
  //console.log(this.data.nameFunc);
  //console.log("index: " + index + "idSms: " + element.idSms + " pid " + element.pid + "numerito: "+ this.id);
  var nameFunc = this.data.nameFunc;
  if (element.idMail == this.data.idMail) {
    pidProcessRunMail.splice(index, 1);
    var prue = spawn.exec('kill -9 ' + element.pid, function (error, stdout, stderr) {
      //var prue = spawn.exec('kill ' + element.pid, function (error, stdout, stderr) {

      if (nameFunc == "pause") {
        connection.query("UPDATE mail SET status = 'paused' WHERE idMail = " + element.idMail, function (err, rows) {
          if (err)
            log(err);
        });
      } else {
        connection.query("UPDATE mail SET status = 'canceled' WHERE idMail = " + element.idMail, function (err, rows) {
          if (err)
            log(err);
        });
      }

      if (error) {
        log('err: ' + error.stack);
        log('otro Error code: ' + error.code);
        log('otro Signal received: ' + error.signal);
      }
      log('otro Child Process STDOUT: ' + stdout);
      log('otro Child Process STDERR: ' + stderr);
    });
  }
}

function stopChildProcessCampaign(element, index) {
  //console.log(this.data.nameFunc);
  //console.log("index: " + index + "idSms: " + element.idSms + " pid " + element.pid + "numerito: "+ this.id);
  var nameFunc = this.data.nameFunc;
  if (element.idAutomaticCampaign == this.data.idAutomaticCampaign) {
    pidProcessRunMail.splice(index, 1);
    var prue = spawn.exec('TASKKILL /PID ' + element.pid + ' /F /T', function (error, stdout, stderr) {
      //var prue = spawn.exec('kill ' + element.pid, function (error, stdout, stderr) {

      if (nameFunc == "pause") {
        connection.query("UPDATE automatic_campaign SET status = 'paused' WHERE idAutomaticCampaign = " + element.idAutomaticCampaign, function (err, rows) {
          if (err)
            log(err);
        });
      } else {
        connection.query("UPDATE automatic_campaign SET status = 'canceled' WHERE idAutomaticCampaign = " + element.idAutomaticCampaign, function (err, rows) {
          if (err)
            log(err);
        });
      }

      if (error) {
        log('err: ' + error.stack);
        log('otro Error code: ' + error.code);
        log('otro Signal received: ' + error.signal);
      }
      log('otro Child Process STDOUT: ' + stdout);
      log('otro Child Process STDERR: ' + stderr);
    });
  }
}

var contPidMail = 0;
function messagesSent() {
  for (contPidMail = 0; contPidMail < pidProcessRunMail.length; contPidMail++) {

    var query = connection.query('SELECT messagesSent from mail WHERE idMail = ' + pidProcessRunMail[contPidMail].idMail);
    var res = "";
    query.on('error', function (err) {
      // Handle error, and 'end' event will be emitted after this as well
      log(err);
    }).on('result', function (row) {
      // it fills our array looping on each user row inside the db 
      //console.log(pidProcessRunMail[contPidMail]);
      pidProcessRunMail[contPidMail - 1].messagesSent = row.messagesSent;
    });
  }
}

//SMS TWOWAY
function searchSmsTwoway(data) {
  var varReturn = false;
  if (pidProcessRunSmsTwoWay.length == 0) {
    return false;
  } else {
    //Validar si existe un proceso con ese id de sms
    for (i in pidProcessRunSmsTwoWay) {
      if (data.idSmsTwoway == pidProcessRunSmsTwoWay[i].idSmsTwoway) {
        varReturn = true;
        break;
      }
    }
  }
  return varReturn;
}

function CountSentSmsTwoway(idSmsTwoway) {
  var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/CountSentSmsTwoway.php ') + idSmsTwoway, function (error, stdout, stderr) {
    if (error) {
      log('err[CountSentSmsTwoway]: ' + error.stack);
      log('Error code[CountSentSmsTwoway]: ' + error.code);
      log('Signal received[CountSentSmsTwoway]: ' + error.signal);
    }
    log('Child Process STDOUT[CountSentSmsTwoway]: ' + stdout);
    log('Child Process STDERR[CountSentSmsTwoway]: ' + stderr);
//    app.io.sockets.emit('respuesta', stdout);
  });
}

function stopChildProcessSmsTwoway(element, index) {
//  console.log(element);
//  console.log("index: " + index + "idSms: " + element.idSms + " pid " + element.pid + "numerito: " + this.data.id);
  var nameFunc = this.data.func;
//  var idSms = element.idSms
  if (element.idSmsTwoway == this.data.idSmsTwoway) {
    pidProcessRunSmsTwoWay.splice(index, 1);
    var prue = spawn.exec('TASKKILL /PID ' + element.pid + ' /F /T', function (error, stdout, stderr) {
      console.log(error);
      console.log(stdout);
      console.log(stderr);
      //var prue = spawn.exec('kill ' + element.pid, function (error, stdout, stderr) {

      if (nameFunc == "paused") {
        connection.query("UPDATE smstwoway SET status = 'paused' WHERE idSmsTwoway = " + element.idSmsTwoway, function (err, rows) {
          if (err) {
            log(err);
          } else {
            var dataPrueba = {idSms: element.idSmsTwoway, status: 'paused'};
            app.io.sockets.emit('refresh-view-sms-two-way', dataPrueba);
            CountSentSmsTwoway(element.idSmsTwoway);
          }
        });
      } else {
        connection.query("UPDATE smstwoway SET status = 'canceled' WHERE idSmsTwoway = " + element.idSmsTwoway, function (err, rows) {
          if (err) {
            log(err);
          } else {
            var dataPrueba = {idSms: element.idSmsTwoway, status: 'canceled'};
            app.io.sockets.emit('refresh-view-sms-two-way', dataPrueba);
            CountSentSmsTwoway(element.idSmsTwoway);
          }
        });
      }

      if (error) {
        log('err: ' + error.stack);
        log('otro Error code: ' + error.code);
        log('otro Signal received: ' + error.signal);
      }
      log('otro Child Process STDOUT: ' + stdout);
      log('otro Child Process STDERR: ' + stderr);
    });
  }
}

function restartSendSmsTwoway(data) {
  connection.query("UPDATE smstwoway SET status='scheduled' WHERE idSmsTwoway = " + data.idSmsTwoway, function (err, rows) {
    if (err) {
      log(err);
    } else {
      var dataPrueba = {idSms: data.idSmsTwoway, status: 'scheduled'};
      app.io.sockets.emit('refresh-view-sms-two-way', dataPrueba);
    }
  });
}
;

//Funcion para preguntar al Email si tiene nuevos correos
function senderSmsxEmail() {
  console.log("senderSmsxEmail");
  var childProcSmsxEmail = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/SenderSmsxEmail.php '));
}
;

function SendSmsFlash() {
  var childProcSmsxEmail = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/maintenance/SendSmsFlash.php '));
};
//Funcion para preguntar si se ejecutando un proceso con el idMail
function searchPidMailProcess(idMail){
  return pidProcessRunMail.find(mail => mail.idMail === idMail);
}
//Funcion para preguntar si se ejecutando un proceso con el idAutomaticCampaign
function searchPidAutomaticProcess(idAutomaticCampaign){
  return pidProcessRunCampaign.find(automatic => automatic.idAutomaticCampaign === idAutomaticCampaign);
}
//Funcion para preguntar si se ejecutando un proceso con el idAutomaticCampaignStep
function searchPidAutomaticStepProcess(idAutomaticCampaignStep){
  return pidProcessRunCampaignStep.find(automaticstep => automaticstep.idAutomaticCampaignStep === idAutomaticCampaignStep);
}

setInterval(function () {
  console.log(sms);
  console.log(pidProcessRunSms);
  console.log("Numero de procesos de SMS: " + countProcessSms);

  console.log(smstwoway);
  console.log(pidProcessRunSmsTwoWay);
  console.log("Numero de procesos de SMS Eppah: " + countProcessSmsTwoWay);

  console.log(mail);
  console.log(pidProcessRunMail);
  console.log("Numero de procesos de MAIL: " + countProcessMail);

  console.log(campaign);
  console.log(pidProcessRunCampaign);
  console.log("Numero de procesos de CAMPAIGN: " + countProcessCampaign);

  console.log(campaignStep);
  console.log(pidProcessRunCampaignStep);
  console.log("Numero de procesos de CAMPAIGNSTEP: " + countProcessCampaignStep);

  console.log(pidProcessRunImport);
  console.log("Numero de procesos de Importacion de contactos: " + countProcessImport);
}, 1000);

setInterval(pollingLoopSms, 3500);
setInterval(verifySendSms, 4500);

setInterval(pollingLoopSmsTwoWay, 4000);
setInterval(verifySendSmsTwoWay, 5000);

setInterval(pollingLoopMailStatisticNotification, 4000);
setInterval(verifySendMailStatisticNotification, 5000);

setInterval(pollingLoopSmsContact, 4000);
setInterval(verifySendSmsContact, 5000);

setInterval(pollingLoopMail, 4000);
setInterval(verifySendMail, 5000);

setInterval(pollingLoopAutomaticCampaign, 4000);
setInterval(verifySendCampaign, 5000);

setInterval(pollingLoopAutomaticCampaignStep, 4000);
setInterval(verifySendCampaignStep, 5000);

setInterval(pollingLoopAutoresponder, 4000);
setInterval(verifySendAutoresponder, 5000);

setInterval(pollingLoopSmsAutoresponder, 4000);
setInterval(verifySendSmsAutoresponder, 5000);


setInterval(senderSmsxEmail, 10000);
setInterval(SendSmsFlash, 300000);
setInterval(pollingLoopMailSending, 10000);

app.io.on('connection', function (socket) {
//  console.log('a user connected');
  socket.emit('messages', messages);
  //socket.emit('saludo', "hola desde node");

  socket.on('communication-php-node', function (data) {
    app.io.sockets.emit(data.callback, data.data);
  });
  var process = setInterval(function () {
    //messagesSent();
    var arrayProcess = {
      processMail: pidProcessRunMail,
      processSms: pidProcessRunSms,
      processImport: pidProcessRunImport
    };

    //socket.volatile.emit('processMail', pidProcessRunMail);
    socket.volatile.emit('process', arrayProcess);
  }, 1000);

  console.log('Number of connections:' + connectionsArray.length);
  // start the polling loop only if at least there is one user connected
  if (!connectionsArray.length) {
    pollingLoopSms();
  }

  socket.on('disconnect', function () {
    clearInterval(process);
    var socketIndex = connectionsArray.indexOf(socket);
    console.log(sms);
    console.log('socket = ' + socketIndex + ' disconnected');
    if (socketIndex >= 0) {
      connectionsArray.splice(socketIndex, 1);
    }
  });

  console.log('A new socket is connected!');
  connectionsArray.push(socket);

  var date = new Date();
  console.log(date);

  socket.on('pause-send-sms', function (data) {
    console.log(data);
    stopSendSms(data);
    //app.io.sockets.emit('respuesta', messages);
  });

  socket.on('cancel-send-sms', function (data) {
    stopSendSms(data);
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('pause-send-mail', function (data) {
    //messages.push(data);
    console.log(data);
    stopSendMail(data);
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('cancel-send-mail', function (data) {
    console.log(data);
    stopSendMail(data);
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('restart-send-mail', function (data) {
    //messages.push(data);
    console.log(data);
    restartSendMail(data);
    //app.io.sockets.emit('respuesta', messages);
  });

  socket.on('pause-automatic-campaign', function (data) {
    //messages.push(data);
    console.log(data);
    stopAutomaticCampaign(data);
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('cancel-automatic-campaign', function (data) {
    console.log(data);
    stopAutomaticCampaign(data);
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('restart-automatic-campaign', function (data) {
    //messages.push(data);
    console.log(data);
    restartCampaign(data);
    //app.io.sockets.emit('respuesta', messages);
  });

  socket.on('id-importcontactfile', function (data) {
    //messages.push(data);
    //console.log(data);
    importStart(data);
    //app.io.sockets.emit('respuesta', messages);
  });

  socket.on('restart-send-sms', function (data) {
    //messages.push(data);
    console.log(data);
    restartSendSms(data);
    //app.io.sockets.emit('respuesta', messages);
  });

  socket.on('stop-server-node', function (data) {
    console.log(data);
    stopServerNode();
    app.io.sockets.emit('respuesta', data);
  });

  socket.on('restart-server-node', function (data) {
    console.log(data);
    restartServerNode();
    app.io.sockets.emit('respuesta', data);
  });

  //DOBLE VIA
  socket.on('search-sms-twoway', function (data) {
    console.log("funcion que busca procesos de smstwoway");
    var booleanProcess = searchSmsTwoway(data);
//    app.io.sockets.emit('process-sms-twoway', booleanProcess);
  })
  //funcion que cancela el envio de smstwoway
  socket.on('stop-sms-twoway', function (data) {
    var booleanProcess = searchSmsTwoway(data);
    if (booleanProcess) {
      console.log("si existe un proceso");
      pidProcessRunSmsTwoWay.forEach(stopChildProcessSmsTwoway.bind(
              {data: data}
      ));
    } else {
      console.log("si no existe proceso los pausa");
      changeStatusSmsTwoway(data);
    }
    app.io.sockets.emit('process-sms-twoway', {response: "Se ha cancelado el envio de Sms doble via.", type: "1"});
  });
  socket.on('paused-sms-twoway', function (data) {
    console.log('paused-sms-twoway', data);
    var booleanProcess = searchSmsTwoway(data);
    console.log("validar si hay procesos", booleanProcess);
    if (booleanProcess) {
      console.log("si existe un proceso");
      pidProcessRunSmsTwoWay.forEach(stopChildProcessSmsTwoway.bind(
              {data: data}
      ));
    } else {
      console.log("si no existe proceso los pausa");
      changeStatusSmsTwoway(data);
    }


    app.io.sockets.emit('process-sms-twoway', {response: "Se ha pausado el envio de Sms doble via.", type: "2"});
  });

  socket.on('resume-sms-twoway', function (data) {
    console.log('resume-sms-twoway', data);
    restartSendSmsTwoway(data);
    app.io.sockets.emit('process-sms-twoway', {response: "Se ha reanudado el envio de Sms doble via.", type: "3"});
  });

  socket.on('create-segment', function (data) {
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/segments/SegmentsHandler.php ') + data.idSegment, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      app.io.sockets.emit('respuesta', stdout);
    });
  });

  socket.on('export-contacts', function (data) {
    var childProc = spawn.exec('php ' + path.join(__dirname, '../../../../bgprocesses/contacts/Export.php ') + data.data2, function (error, stdout, stderr) {
      if (error) {
        log('err: ' + error.stack);
        log('Error code: ' + error.code);
        log('Signal received: ' + error.signal);
      }
      log('Child Process STDOUT: ' + stdout);
      app.io.sockets.emit('respuesta', stdout);
    });
  });

});

module.exports = app;
