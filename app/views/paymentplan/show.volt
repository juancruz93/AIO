<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{data.name}}"}}
    </div>            
    <hr class="basic-line">
    <p>
      {{"{{data.description}}"}}
    </p>            
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a href="{{url('paymentplan#/')}}" class="button btn btn-md default-inverted"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar al listado de planes de pago</a>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        <span style="font-size: 1.7em">Detalle</span>
      </div>
      <div class="body">
        <table class="table table-striped">
          <tr>
            <th>País:</th>
            <td>{{"{{data.country}}"}}</td>
          </tr>
          <tr>
            <th>Tipo:</th>
            <td>{{"{{data.type == 'public' ? 'Público' : 'Privado'}}"}}</td>
          </tr>
          <tr>
            <th>Espacio:</th>
            <td>{{"{{data.diskSpace}}"}} MB</td>
          </tr>
          <tr>
            <th>Impuestos:</th>
            <td>
          <li data-ng-repeat=" im in data.tax">{{"{{im.name}}"}}</li>
          </td>
          </tr>
          <tr>
            <th>Servicios:</th>
            <td>
          <li data-ng-repeat="serv in data.services">{{"{{serv.Service}}"}}</li>
          </td>
          </tr>
          <tr>
            <th>Estado:</th>
            <td>{{"{{data.status == 1 ? 'Activo' : 'Inactivo'}}"}}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        <span style="font-size: 1.7em">Servicios</span>
      </div>
      <div class="body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id="myTabs" data-ng-show="hr">
          <li role="presentation" data-ng-class="tabsms == true ? 'active':''" data-ng-show="tabsms"><a href="#sms" aria-controls="sms" role="tab" data-toggle="tab">SMS</a></li>
          <li role="presentation" data-ng-class="tabemail == true ? tabsms == true ? '':'active':''" data-ng-show="tabemail"><a href="#email_marketing" aria-controls="email_marketing" role="tab" data-toggle="tab">Email Marketing</a></li>
          <li role="presentation" data-ng-class="tabsmstwoway == true ? tabemail == true ? tabsms == true ? '':'active':'active':''" data-ng-show="tabsmstwoway"><a href="#smstwoway" aria-controls="smstwoway" role="tab" data-toggle="tab">SMS doble-via</a></li>
          <li role="presentation" data-ng-class="tablandingpage == true ? tabemail == true ? tabsms == true ? '':'active':'active':''" data-ng-show="tablandingpage"><a href="#landingpage" aria-controls="landingpage" role="tab" data-toggle="tab">Landing Page</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <!-- PRIMER TAB SMS -->
          <div role="tabpanel" class="tab-pane fade" id="sms" data-ng-class="tabsms == true ? 'in active':''">
            <br>
            <div data-ng-if="tabsms">
              <table class="table table-striped table-condensed">
                <tr>
                  <th>Tipo de plan</th>
                  <td>{{"{{sms.Plantype}}"}}</td>
                </tr>
                <tr>
                  <th>Lista de precios</th>
                  <td>{{"{{sms.namePriceList}}"}}</td>
                </tr>
                <tr>
                  <th>Adaptadores</th>
                  <td>
                <li data-ng-repeat="ad in sms.adapter">{{"{{ad.name}}"}}</li>
                </td>
                </tr>
                <tr>
                  <th>Cantidad</th>
                  <td>{{"{{sms.amount}}"}}</td>
                </tr>
                <tr>
                  <th>Velocidad</th>
                  <td>{{"{{sms.speed}}"}}</td>
                </tr>
              </table>
            </div>
          </div>

          <!-- Segundo TAB Email  -->
          <div role="tabpanel" class="tab-pane fade" id="email_marketing" data-ng-class="tabemail == true ? tabsms == true ? '':'in active':''">
            <br>
            <div data-ng-if="tabemail">
              <table class="table table-striped table-condensed">
                <tr>
                  <th>Tipo de plan</th>
                  <td>{{"{{email.Plantype}}"}}</td>
                </tr>
                <tr>
                  <th>Lista de precios</th>
                  <td>{{"{{email.namePriceList}}"}}</td>
                </tr>
                <tr>
                  <th>Modo</th>
                  <td>{{"{{email.accountingMode == 'sending' ? 'Envío' : 'Contacto'}}"}}</td>
                </tr>
                <tr>
                  <th>Mta</th>
                  <td>
                <li data-ng-repeat="mta in email.mta">{{"{{mta.name}}"}}</li>
                </td>
                </tr>
                <tr>
                  <th>Urldomain</th>
                  <td>
                <li data-ng-repeat="url in email.urldomain">{{"{{url.name}}"}}</li>
                </td>
                </tr>
                <tr>
                  <th>MailClass</th>
                  <td>
                <li data-ng-repeat="mc in email.mailClass">{{"{{mc.name}}"}}</li>
                </td>
                </tr>
                <tr>
                  <th>Cantidad</th>
                  <td>{{"{{email.amount}}"}}</td>
                </tr>
              </table>
            </div>
          </div>
          <!-- FIN SEGUNDO TAB -->

          {#TAB SMStwoway#}

          <div role="tabpanel" class="tab-pane fade" id="smstwoway" data-ng-class="tabsmstwoway == true ? tabemail == true ? tabsms == true ? '':'in active':'in active':''">
            <br>
            <div data-ng-if="tabsmstwoway">
              <table class="table table-striped table-condensed">
                <tr>
                  <th>Tipo de plan</th>
                  <td>{{"{{smstwoway.Plantype}}"}}</td>
                </tr>
                <tr>
                  <th>Lista de precios</th>
                  <td>{{"{{smstwoway.namePriceList}}"}}</td>
                </tr>
                <tr>
                  <th>Cantidad</th>
                  <td>{{"{{smstwoway.amount}}"}}</td>
                </tr>
              </table>
            </div>
          </div>

          {#TAB LandingPage#}
            <div role="tabpanel" class="tab-pane fade" id="landingpage" data-ng-class="tablandingpage == true ? tabemail == true ? tabsms == true ? '':'in active':'in active':''">
              <br>
                <table class="table table-striped table-condensed">
                  <tr>
                    <th>Tipo de plan</th>
                    <td>{{"{{landingpage.Plantype}}"}}</td>
                  </tr>
                  <tr>
                    <th>Lista de precios</th>
                    <td>{{"{{landingpage.namePriceList}}"}}</td>
                  </tr>
                </table>
              </div>
        </div>

      </div>
    </div>
  </div>
</div>
</div>
<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
    $('#myTabs a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    });
  });
</script>