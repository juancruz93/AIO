<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Previsualización</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('themes/default/images/favicons/favicon48x48.ico')}}">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->

    <!--headerIncludes-->

    <link href="{{url('library/htmlbuilder/elements/bundles/original_skeleton.css')}}" rel="stylesheet"></head>
  <body>
    {% for item in blocks %}
      {{item.frames_content}}
    {% endfor %}
    {{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}
    {{ javascript_include('library/bootstrap-3.3.4/js/bootstrap.min.js') }}
    <script type="text/javascript">
      var divProtectYoutube = document.querySelector("div[data-type=video]");
      if (divProtectYoutube !== null) {
        var padre = divProtectYoutube.parentNode;
        padre.removeChild(divProtectYoutube);
      }
      $('.carousel').carousel();
      $(".over .overlay").remove();
    </script>
  </body>
</html>
