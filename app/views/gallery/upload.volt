{% extends "templates/default.volt" %}
{% block js %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  {# Dropzone Master #}
  {{ stylesheet_link('library/dropzone-master/css/dropzone.min.css') }}
  {{ javascript_include('library/dropzone-master/js/dropzone.min.js') }}
  <script>
    $(function () {
      var img = "{{url('')}}images/general/images.png";
      var arc = "{{url('')}}images/general/archives.png";
      $("div#images").dropzone({
        url: "{{url('gallery/uploadimage')}}",
        paramName: "file", // The name that will be used to transfer the file
        method: "post",
        maxFilesize: 2, // MB
        dictDefaultMessage: "Arrastre y suelte imagenes aquí o haga clic aqui para seleccionarlas y empezar a cargarlas <br><br> <img src='" + img + "' width='270' />",
        dictFallbackMessage: "El navegador que estas usando no soporta el método 'Arrastre y suelte' para cargar archivos, te recomendamos usar Google Chrome",
        dictFileTooBig: "El archivo que intenta cargar es demasiado grande ({{ '{{filesize}}' }} MB), debe ser igual o inferior a {{ '{{maxFilesize}}' }} MB",
        addRemoveLinks: true,
        dictRemoveFile: "Remover"
      });

      $("div#files").dropzone({
        url: "{{url('gallery/uploadfile')}}",
        paramName: "file", // The name that will be used to transfer the file
        method: "post",
        maxFilesize: 2, // MB
        dictDefaultMessage: "Arrastre y suelte archivos aquí o haga clic aqui para seleccionarlos y empezar a cargarlos <br><br> <img src='" + arc + "' width='270' />",
        dictFallbackMessage: "El navegador que estas usando no soporta el método 'Arrastre y suelte' para cargar archivos, te recomendamos usar Google Chrome",
        dictFileTooBig: "El archivo que intenta cargar es demasiado grande ({{ '{{filesize}}' }} MB), debe ser igual o inferior a {{ '{{maxFilesize}}' }} MB",
        addRemoveLinks: true,
        dictRemoveFile: "Remover"
      });
    });
  </script>    
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Cargar imágenes u otros archivos en el servidor
      </div>            
      <hr class="basic-line" />
      <p>
        En esta sección podrá cargar imágenes y archivos al servidor. De esta manera podrá administrar una galería
        llena de recursos que podrá usar en la creación y edición de correos (Imágenes) y adjuntar archivos en sus envíos
      </p>         
    </div>
  </div> 

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
      <a href="{{url('gallery')}}" class="btn default-inverted">
        <i class="fa fa-arrow-left"></i>
        Regresar
      </a>
    </div>    
  </div>  

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
              <h4 class="panel-title text-center">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Utiliza este espacio únicamente para cargar <span style="color: #ff6e00;">imágenes</span>
                  <br>
                  <h5><i>Se recomienda que el ancho de la imagen sea de máximo 600px</i></h5>
                </a>
              </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <div class="panel-body">
                <div id="images" class="dropzone"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
              <h4 class="panel-title text-center">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                  Utiliza este espacio únicamente para cargar <span style="color: #ff6e00;">archivos</span> que no sean imágenes
                  <br>
                  <h5><i>Se recomienda que el tamaño del archivo sea de máximo 2MB</i></h5>
                </a>
              </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body">
                <div id="files" class="dropzone"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
