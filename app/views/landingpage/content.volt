<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">

      <em>Contenido de la Landing Page</em>
    </div>
    <br>
    <p class="small-text">
      Tienes varias opciones que puedes elegir para crear el contenido de tu Landing Page, te recomendamos el editor
      avanzado, crearás contenido de manera rápida y fácil.
    </p>
  </div>
</div>

<div class="row" ng-cloak>
  <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      <div class="block block-info">
        <div class="body text-center">
          <div class="wrap">
            <md-progress-linear md-mode="query" data-ng-show="misc.loader" class="md-warn"></md-progress-linear>
          </div>
          <div class="row" id="options" data-ng-show="misc.options">
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2 col-lg-offset-4">
              <ul class="ch-grid ">
                <li>
                  <div class="ch-item edit-avanz pointer-cursor margin-botton">
                    <a href="{{ url('landingpage/pagebuilder') }}/{{ '{{ idLandingPageGet}}' }}">
                      <div class="ch-info">
                        <h3>Landing Page en blanco</h3>
                      </div>
                    </a>
                  </div>
                  <b>Landing Page en blanco</b>
                </li>
                <p class="text-justify margin-top">Crea el contenido desde cero de manera rápida y              
                  fácil.</p>
              </ul>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
              <ul class="ch-grid">
                <li >
                  <div class="ch-item template pointer-cursor margin-botton">
                    <a href="{{url('landingpagetemplate#/selecttemplate')}}/{{'{{ idLandingPageGet}}'}}" class="text-center">
                      <div class="ch-info">
                        <h3>Landing Page prediseñadas</h3>
                      </div>
                    </a>
                  </div>
                  <b>Landing Page prediseñadas</b>
                </li>
                <p class="text-justify margin-top">Elige una plantilla como base para el contenido de su Landing.</p>
              </ul>
            </div>
          </div>
          <div class="row" id="content" data-ng-show="misc.content">
            <div class="col-md-12">
              <img src="{{"{{thumbnail}}"}}" />
            </div>
            <div class="col-md-12">
              <a href="{{ url('landingpage/pagebuilder') }}/{{ '{{ idLandingPageGet}}' }}" class="text-center">
                <h3>Click aquí para editar contenido</h3>
              </a>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <a href="{{ url('landingpage') }}"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Salir">
            Salir
          </a>
          <a ui-sref="create.describe({idLandingPage:idLandingPageGet})"
             class="button btn btn-small info-inverted"
             data-toggle="tooltip" data-placement="top" title="Atrás">
            Atrás
          </a>
          <a ui-sref="create.confirmation({idLandingPage:idLandingPageGet})"
                  class="button btn btn-small success-inverted"
                  data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
            Continuar
          </a>
        </div>
      </div>
    </div>
  </form>
</div>

