<!DOCTYPE html>
<html lang="en">

  <head>
    {{getTitle()}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('themes/default/images/favicons/favicon48x48.ico')}}">
    <link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
    <link rel="stylesheet" href="{{url('css/errors.min.css')}}">
  </head>

  <body class="body-errors">
    <div class="errorPage">
      <div class="errorPage-container">
        <div class="errorPage-topImage">
          <figure class="errorPage-image">
            <img src="{{url('images/aio/aio-fly-front.png')}}" alt=""/>
          </figure>
        </div>
        <div class="errorPage-text">
          {% block content %}

          {% endblock %}
        </div>
      </div>
    </div>
  </body>

</html>