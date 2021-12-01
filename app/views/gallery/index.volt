{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Dialogs #}
{% endblock %}

{% block js %}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="row">
        <div class="col-md-6">
          <div class="title">
            Galería de archivos
          </div>
        </div>    
        <div class="col-md-6 text-right">
          <p class="asset-indicator">
            <span class="glyphicon glyphicon-picture"></span> {{space}} MB /{{account.diskSpace}} MB
          </p>   
        </div>    
      </div>

      <hr class="basic-line" />
      <p>
        Aqui encontrará un listado con todas las imágenes y/o archivos que ha subido a la plataforma o que ha usado alguna vez. También podra subir nuevos archivos
        y eliminar aquellos que ya no usa para liberar espacio.
      </p>            
    </div>
  </div>    

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right"> 
      <a href="{{url('gallery/upload')}}" class="button shining btn btn-md success-inverted">
        Subir archivo o imagen
      </a>
    </div>    
  </div>    


  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">            
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'gallery/index']) }}
      </div>
    </div>    

    <div class="main">
      <ul id="og-grid" class="og-grid">
        {% for item in page.items %}
          <li >
            {% if item.type == "Image" %}
              
              {% set image = url.get("gallery/show/" ~ item.idAsset ) %}
              {% set thumbnail = url.get("gallery/thumbnail/" ~ item.idAsset ) %}

            {% elseif item.type == "File" %}
              {% set img = path.path ~ 'public/images/gallery/' ~ item.extension ~ '.png' %}
              {%  if file_exists.fileExists(img)  %}
                {% set image =  url.get()~'images/gallery/' ~ item.extension ~ '.png'   %}
              {% else %}
                {% set image = url.get()~'images/gallery/unknown.png'  %}
              {% endif %}
              {% set thumbnail = image  %}
            {% endif %}
            
            <a href="{{url('gallery/delete')}}/{{item.idAsset}}/{{page.current}}" data-largesrc="{{image}}" data-title="{{item.name}}" data-description="" data-date="{{date('d/M/Y H:i', item.created)}}" data-size="<?php echo round($item->size/1048576, 2); ?>MB" data-dimensions="{{item.dimensions}}">
              <img src="{{thumbnail}}" alt="{{item.name}}" onerror="this.onerror=null;this.src='{{ url.get()~'images/gallery/unknown.png' }}';"/>
            </a>
            <div class="deleter-button">
              <span class="glyphicon glyphicon-trash" id="delete" onClick="openModal();" data-toggle="tooltip" data-placement="top" title="Borrar este recurso" data-id="{{url('gallery/delete')}}/{{item.idAsset}}/{{page.current}}"></span>
            </div>    
          </li>
        {% endfor %}
      </ul>
    </div>   

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">            
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'gallery/index']) }}
      </div>
    </div>        
  {% else %}  
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="text-center pull-center">
        <img src="{{url('')}}images/general/no-files-in-gallery.png" width="350" />
      </div>
    </div>    
  {% endif %}     
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right"> 
      <a href="{{url('')}}" class="button shining btn btn-md danger-inverted">
        Regresar
      </a>
      <a href="{{url('gallery/upload')}}" class="button shining btn btn-md success-inverted">
        Subir archivo o imagen
      </a>
    </div>    
  </div>    

  <div id="somedialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Esta seguro?</h2>
        <div>                    
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <iframe id="zoom-it" src="#" width="100%" height="700"></iframe>
      </div>
    </div>
  </div>            

  <script>
    $(document).on("click", "#delete", function () {
      var myURL = $(this).data('id');
      $("#btn-ok").attr('href', myURL);
    });

    function openModal() {
      $('.dialog').addClass('dialog--open');
    }

    function closeModal() {
      $('.dialog').removeClass('dialog--open');
    }


    function zoomImage(id) {
      var url = "{{url('gallery/show')}}/" + id;
      $("#zoom-it").attr('src', url);
    }

    $(function () {
      $('#tooltip').tooltip();
    });
  </script>  
{% endblock %}
{% block footer %}
  {# gallery #}
  {{ javascript_include('library/thumbnail-grid-expanding-preview/js/modernizr.custom.js') }}
  {{ stylesheet_link('library/thumbnail-grid-expanding-preview/css/component.min.css') }}
  {{ javascript_include('library/thumbnail-grid-expanding-preview/js/grid.min.js') }}
  <script>
    $(function () {
      Grid.init();
    });
  </script>
{% endblock %}
