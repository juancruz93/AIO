<style>
  .menu-footer2 {
    position: absolute;
{#    bottom: 4px;#}
{#    right: 16px;#}
{#    font-size: 0.8em;#}
{#    z-index: 16;#}
  }

</style>
<div style="position: absolute; left: 16px;">
  {#<img src="{{url('')}}images/img_footer.png" width="110"/>    #}  
 {# <div class="copy" style="padding: 10px;">
    {{theme.footer}}
  </div>#}
{{personalizedCss.getLeftBlock()}}

</div>
<div class="menu-footer">
{#   <div class="social-network" style="padding: 10px;">
    <a href="https://es-es.facebook.com/SigmaMovil" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en facebook">
      <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/facebook-icon.png" />
    </a>
    <a href="https://twitter.com/SigmaMovil" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en twitter">
      <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/twitter-icon.png" />
    </a>
    <a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en youtube">
      <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/youtube-icon.png" />
    </a>
    <a href="https://plus.google.com/+Sigmamovil/posts" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en google plus">
      <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/google-plus-icon.png" />
    </a>
    <a href="https://www.linkedin.com/company/sigma-m-vil-s.a." class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en linkedin">
      <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/linkedin-icon.png" />
    </a>
  </div>#}

    {{personalizedCss.getRightBlock()}}

</div>  
<ul class="item-menu-container dashed-effect dashed-effect-b per-dashed-effect-customized per-icon-footer-color">
  {% for item in smartMenu.get() %}
    <li class="shining" data-toggle="tooltip_default" data-placement="top" title="{{item.title}}">
      <a href="{{ url(item.url) }}" target="{{item.target}}" class="hi-icon {{item.icon}} {{item.class}} per-hi-icon-customized"></a>
    </li>
  {% endfor %} 
</ul>
