{% set icon = 'glyphicon glyphicon-info-sign' %}
{% if message.type == 'error' %}
    {% set icon = 'glyphicon glyphicon-remove' %}
{% elseif message.type == 'success' %}   
    {% set icon = 'glyphicon glyphicon-ok' %}
{% elseif message.type == 'warning' %}   
    {% set icon = 'glyphicon glyphicon-warning-sign' %}
{% endif %}    
