<div class="clearfix"></div>
<div class="space"></div>   

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        
    </div>    
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="block block-danger">
            <div class="header">Eliminar una lista de contactos</div>
            <div class="body">
                <p>
                    ¿Esta seguro que desea eliminar la lista de contactos <strong><em>{{ '{{contactlist.name}}' }}</em></strong>?
                </p>
                <p>
                    Recuerde que si la elimina, no se podrán recuperar los datos
                </p>
            </div>
            <div class="footer">
                <a href="#/" class="button shining btn btn-sm default-inverted">Regresar</a>
                <button ng-click="deleteContactlist()" class="button shining btn btn-sm danger-inverted">Eliminar</button>
            </div>
        </div>
    </div>
</div>
