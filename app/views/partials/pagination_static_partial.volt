{# 
	************************************************************
	Partial para paginacion que no esta hecho con EMBER
	Parametros: pagination_url
	Ejemplo:
	partial('partials/pagination_static_partial', ['pagination_url': 'index/index'])
	************************************************************
#}
<div id="pagination" class="text-center">
    <ul class="pagination">
        <li class="{{ (page.current == 1)?'disabled':'enabled' }}">
            <a href="{{ url(pagination_url) }}?page=1" class="new-element {{ (page.current == 1)?'disabled':'enabled' }}"><i class="glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li class="{{ (page.current == 1)?'disabled':'enabled' }}">
            <a href="{{ url(pagination_url) }}?page={{ page.before }}" class="new-element {{ (page.current == 1)?'disabled':'enabled' }}"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
            <span><b>{{page.total_items}}</b> registros </span><span>Página <b>{{page.current}}</b> de <b>{{page.total_pages}}</b></span>
        </li>
        <li class="{{ (page.current >= page.total_pages)?'disabled':'enabled' }}">
            <a href="{{ url(pagination_url) }}?page={{page.next}}" class="new-element {{ (page.current >= page.total_pages)?'disabled':'enabled' }}"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li class="{{ (page.current >= page.total_pages)?'disabled':'enabled' }}">
            <a href="{{ url(pagination_url) }}?page={{page.last}}" class="new-element {{ (page.current >= page.total_pages)?'disabled':'enabled' }}"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
    </ul>
</div>
        
<script type="text/javascript">
    $(function () {
       $('.disabled').click(function () {return false;});
    });
</script>    
