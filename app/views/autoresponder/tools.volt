
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>  

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="body row text-center">
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2 col-lg-offset-3">
                <ul class="ch-grid ">
                    <li>
                        <div class="ch-item smsxlote pointer-cursor margin-botton" class="disabled">
                            <a ui-sref="birthdaysms">
                                <div class="ch-info">
                                    <h3>Autorespuesta de SMS</h3>
                                </div>
                            </a>
                        </div>
                        <b>Autorespuesta de SMS</b>
                    </li>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <ul class="ch-grid">
                    <li >
                        <div class="ch-item smsxcsv pointer-cursor margin-botton">
                            <a ui-sref="birthday" class="text-center" disabled>
                                <div class="ch-info">
                                    <h3>Autorespuesta de E-mail</h3>
                                </div>
                            </a>
                        </div>
                        <b>Autorespuesta de E-mail</b>
                    </li>
                </ul>
            </div>
            {#<div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <ul class="ch-grid">
                    <li>
                        <div class="ch-item smsxlista pointer-cursor margin-botton">
                            <a ui-sref="createdcontact" >
                                <div class="ch-info">
                                    <h3>Crear SMS doble vía por lista de contacto o segmento</h3>
                                </div>
                            </a>
                        </div>
                        <b>Crear SMS doble vía por lista de contacto o segmento</b>
                    </li>
                </ul>
            </div>#}
        </div>
    </div>
</div>

              {#<center><a ui-sref="edit">EditarSms doble via</a></center>#}
            {% endblock %}