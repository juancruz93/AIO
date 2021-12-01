<!DOCTYPE html>
<html lang="es" >
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link href='https://fonts.googleapis.com/css?family=Questrial' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" type="image/x-icon" href="{{url('')}}themes/{{theme.name}}/images/favicons/favicon48x48.ico">
        {{ stylesheet_link('library/bootstrap-3.3.4/css/bootstrap.css') }}
        <title>Felicitaciones</title>
    </head>
    <body>

        <div class="container text-center" >
            <h1>{{msg}}</h1>
            <a href="{{url}}">SALIR</a>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
        {{ javascript_include('js/angular/survey/appsurvey.js') }}
    </body>
</html>