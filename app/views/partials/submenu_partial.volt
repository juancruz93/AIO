<ul class="ch-grid">
  {% for item in submenu.get() %}
    {% for i in item %}
      {% if submenu.key() == i["controllerCurrent"] %}
        {% if in_array(user.Role.name, i["roles"]) or i["roles"][0] == "all" or i["roles"][0] == user.idRole%}
          <li onclick="location.href = '{{url(i["url"])}}';">
            <div class="ch-item {{i["class"]}} pointer-cursor">
              <div class="ch-info">
                <h3>{{i["title"]}}</h3>
              </div>
            </div>
            {{i["title"]}}
          </li>
        {% endif %}
      {% endif %}
    {% endfor %}
  {% endfor %}
</ul>