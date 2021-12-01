{% block header %}
  {{ stylesheet_link('css/popoverStyle.css') }} 
{% endblock %}
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Estadística de Campaña automatica 
    </div>  

    <hr class="basic-line" />
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
        <a href="{{url("automaticcampaign#/")}}" class="btn default-inverted"><i class="fa fa-arrow-left"></i> Regresar</a>
      </div>
    </div>
             
        <div class="fill-block fill-block-info" >
          <div class="header">
            Instrucciones
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones para un entendimiento completo de las estadisticas:
            </p>
          
              <li>Para ver las estadisticas de campaña automatica se debe hacer click sobre cada uno de los nodos y se desplegara la informacion de la estadistica.</li>
              <li>La informacion del nodo de contacto y los nodos operadores es distinta a la informacion de los nodos de servicios.</li>
              Las estadisticas de los nodos de servicios se representa segun una regla de colores que se detallan a continuacion.<br>
              <i class="fa fa-square " style="color: green;" > </i> El color verde representa el total de envios realizados correctamente.<br>
              <i class="fa fa-square " style="color: red;" ></i>  El color rojo represente los envios fallidos.<br>
              <i class="fa fa-square " style="color: blue;" ></i>  El color azul representa los envios rebotados, solo aplica para los envios de email.<br>
              <i class="fa fa-square " style="color: orange;" ></i>  El color anaranjado representa el total de aperturas realizadas, solo aplica para envios de email. <br>
              <i class="fa fa-square " style="color: yellow;" ></i>  El color amarillo representa el total de clicks realizados, solo aplica para envios de email.<br>
              <i class="fa fa-square " style="color: purple;" ></i>  El color purpura representa el total de envios que se han ido a spam, solo aplica para email.<br>
          </div>
        </div> 
 
    <div class="border-blue">

      <flow-chart-statistics  style="margin: 5px; width: 100%; height: 4000px;"chart="chartViewModel"></flow-chart-statistics> 

    </div>

  </div>
</div>

{#<a href="#" data-toggle="popover" title="Popover Header" data-content="Some content inside the popover">Toggle popover</a>#}


{#<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
});
</script>#}


