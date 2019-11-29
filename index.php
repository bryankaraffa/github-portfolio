<?php
$github_Api_Key = $_ENV['github-api-key'];
    
error_reporting(0);

require_once('./includes/github-api-1.4.3/src/github-api.php');
use Milo\Github;

require_once('./includes/php-markdown/Michelf/Markdown.inc.php');
use \Michelf\Markdown;

function url_exists($url) {
    $file = $url;
    $file_headers = @get_headers($file);
    $exists = true;
    foreach ($file_headers as $file_header) {
        if($file_header == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
    }
    return $exists;
}

///// Begin Logic

$api = new Github\Api;
$api->setToken(new Milo\Github\OAuth\Token($github_Api_Key));
$events = $api->get('/users/bryankaraffa/events');
$events = $api->decode($events);

$repos = [];
$readme = [];
foreach ($events as $event) {
    $repo = $api->get('/repos/'.$event->repo->name);
    $repo = $api->decode($repo);

    if (!isset($repos[$repo->id]) && $repo->{'private'} != true ) {
        $repos[$repo->id]=$repo;
        $r=file_get_contents("https://github.com/".$event->repo->name."/raw/master/README.md");
        if ($r != '') {
            $readme[$repo->id]=$r;
        }
        else {
            $readme[$repo->id]='';    
        }                 
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <!--Import Google Icon Font-->
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <style>
      body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
      }
    
      main {
        flex: 1 0 auto;
      }
    </style>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- Matomo -->
    <script type="text/javascript">
      var _paq = _paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//matomo.calcoasttech.com/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', '3']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->    
</head>

<body>
<main>
    <div class="container">
        <!-- Page Content goes here -->
<?php 
    foreach ($repos as $repo) {
        $readme_content = Markdown::defaultTransform($readme[$repo->id]);
        if (url_exists('https://github.com/'.$repo->full_name.'/raw/master/screenshot.png')) {
            $card_image= '
                <div class="card-image waves-effect waves-block waves-light">
                  <img class="activator" src="https://github.com/'.$repo->full_name.'/raw/master/screenshot.png">
                </div>
            ';
        }
        else {
            $card_image='';
        }
        echo ('
            <div class="card hoverable small s12 l6">
                '.$card_image.'
                <div class="card-content">
                    <span class="card-title activator grey-text text-darken-4 truncate">'.$repo->full_name.'<i class="material-icons right">more_vert</i></span>
                    <span class="chip">'.$repo->language.'</span>
                    <span><a href="'.$repo->html_url.'">Last Updated: '.$repo->updated_at.'</a></span>
                </div>
                <div class="card-reveal">
                <span class="card-title grey-text text-darken-4">'.$repo->full_name.'<i class="material-icons right">close</i></span>
                <p>'.$readme_content.'</p>
                </div>
            </div>
            ');
    }
?>
    </div>
    </main>
    <footer class="light-blue page-footer">
          <div class="blue footer-copyright">
            <div class="container">
            Made for fun by <a class="grey-text text-lighten-4" href="//github.com/bryankaraffa">bryankaraffa</a>
            </div>
          </div>
    </footer>
  <!-- Compiled and minified JavaScript -->
  <script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>

</body>
</html>
<?php
// Debugging
//var_dump($repos);
?>      
