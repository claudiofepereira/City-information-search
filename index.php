<?php

$local = null;
$localidade = "Lisboa";

if (!isset($_GET["local"])) {
    // Obter lat & lng da cidade pesquisada
    $geoURl = "http://www.mapquestapi.com/geocoding/v1/address";
    $geoKey = "XFNxQ82etHEFDAjhOSzhE4hozjwbRq45";

    $geo_url = "$geoURl?key=$geoKey&location=$localidade";

    $data_geo_json = file_get_contents($geo_url);
    $data_geo_php = json_decode($data_geo_json);

    $lat = $data_geo_php->results[0]->locations[0]->latLng->lat;
    $lng = $data_geo_php->results[0]->locations[0]->latLng->lng;

    $cidade = $data_geo_php->results[0]->locations[0]->adminArea5;
    $pais = $data_geo_php->results[0]->locations[0]->adminArea1;

    // Obter informação meteorológica da cidade predefinida
    $url_base = "https://api.darksky.net/forecast";
    $apiKey = "814e5b27d87937feb926c8b0178f77c3";

    $params = "units=si";
    $lang = "lang=pt";
    $url = "$url_base/$apiKey/$lat,$lng?$params&$lang";

    $previsaoJson = file_get_contents($url);
    $previsaoPhp = json_decode($previsaoJson);

    $temperatura = floor($previsaoPhp->currently->temperature);
    $tempoAtual = $previsaoPhp->currently->summary;
    $precipProbability = $previsaoPhp->currently->precipProbability;
    $windSpeed = $previsaoPhp->currently->windSpeed;

    $icon = $previsaoPhp->currently->icon;

    $iconUrl = "https://darksky.net/images/weather-icons/$icon.png";
}

if (isset($_GET["local"])) {
    // Obter lat & lng da cidade pesquisada
    $geoURl = "http://www.mapquestapi.com/geocoding/v1/address";
    $geoKey = "XFNxQ82etHEFDAjhOSzhE4hozjwbRq45";
    $location = $_GET["local"];

    $geo_url = "$geoURl?key=$geoKey&location=$location";

    $data_geo_json = file_get_contents($geo_url);
    $data_geo_php = json_decode($data_geo_json);

    $lat = $data_geo_php->results[0]->locations[0]->latLng->lat;
    $lng = $data_geo_php->results[0]->locations[0]->latLng->lng;

    $cidade = $data_geo_php->results[0]->locations[0]->adminArea5;
    $pais = $data_geo_php->results[0]->locations[0]->adminArea1;

    // Obter informação meteorológica da cidade pesquisada
    $url_base = "https://api.darksky.net/forecast";
    $apiKey = "814e5b27d87937feb926c8b0178f77c3";

    $params = "units=si";
    $lang = "lang=pt";
    $url = "$url_base/$apiKey/$lat,$lng?$params&$lang";

    $previsaoJson = file_get_contents($url);
    $previsaoPhp = json_decode($previsaoJson);

    $temperatura = floor($previsaoPhp->currently->temperature);
    $tempoAtual = $previsaoPhp->currently->summary;
    $precipProbability = $previsaoPhp->currently->precipProbability;
    $windSpeed = $previsaoPhp->currently->windSpeed;

    $icon = $previsaoPhp->currently->icon;
    $iconUrl = "https://darksky.net/images/weather-icons/$icon.png";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body onload="loadData()">
    <!-- Section for the search input box -->
    <div class="s130">
        <form autocomplete="off" action="index.php" method="GET">
            <div class="inner-form">
                <div class="input-field first-wrap">
                    <div class="svg-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"></svg>
                    </div>
                    <input id="search" name="local" type="text" placeholder="De que cidade está à procura?" required pattern="\S+" title="This field is required" />
                </div>
                <div class="input-field second-wrap">
                    <button class="btn-search" type="submit">
                        Pesquisar
                    </button>
                </div>
            </div>
            <span class="info">
                ex: New York, Barcelona, Lisboa, London.
            </span>
        </form>
    </div>
    <!-- Section for the weather and google map information -->
    <section class="city-info-container" id="container">
        <div class="info-meteo" id="info-meteo">
            <h1 style="font-size:19px;padding-bottom:1%;padding-left:5%">Dados Regionais</h1>
            <div id="descr-city"></div>
            <div class="img-text-weather">
                <div class="info-text">
                    <img class="img-weather" src="<?php echo $iconUrl ?>">
                    <h2 style="font-size:19px;padding-bottom:1%;padding-left:5%">Informação meteorológica</h2>
                    <p style="font-size:15px"><span style="font-weight:bold;font-size:15px">Temperatura: </span><?php echo  $temperatura . '°C.' ?></p>
                    <p style="font-size:15px"><span style="font-weight:bold;font-size:15px">Previsão temporal: </span><?php echo $tempoAtual ?></p>
                    <p style="font-size:15px"><span style="font-weight:bold;font-size:15px">Probabilidade de ocorrer precipitação: </span><?php echo $precipProbability * 100 . "%" ?></p>
                    <p style="font-size:15px"><span style="font-weight:bold;font-size:15px">Velocidade do vento: </span><?php echo floor($windSpeed) . "m/s" ?></p>
                </div>
            </div>
        </div>
        <div id="map" class="map"></div>
    </section>

    <hr>

    <h1 style="font-size:19px;padding-top:2%;padding-bottom:2%;text-align:center">Notícias Regionais</h1>
    <section class="news-container">
        <div id="news"></div>
    </section>
</body>

<script>
    // Initialize and add the map
    function initMap() {
        <?php
        if ($location) echo "var address='$location';";
        else echo "var address='Lisboa';";
        ?>

        var geocoder = new google.maps.Geocoder();

        // The location of pos
        var pos = {
            lat: 38.722252,
            lng: -9.139337
        };
        // The map, centered at pos
        var map = new google.maps.Map(
            document.getElementById('map'), {
                zoom: 13,
                center: pos
            });
        // The marker, positioned at pos
        var marker = new google.maps.Marker({
            position: pos,
            map: map
        });

        /* a callback function to process the response from geocoding */
        var evtcallback = function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                var _address = results[0].formatted_address;
                var _location = results[0].geometry.location;
                var _lat = _location.lat();
                var _lng = _location.lng();

                console.info('Geocoding succeeded for %s and found address %s [ %s,%s ]', address, _address, _lat, _lng);

                latlng = new google.maps.LatLng(_lat, _lng);
                marker.setPosition(latlng);
                marker.setTitle(_address);

                google.maps.event.addListener(marker, 'click', function(event) {
                    infoWindow.setContent(this.title);
                    infoWindow.open(map, this);
                    infoWindow.setPosition(this.position);
                }.bind(marker));

                map.setCenter(latlng);
                map.setZoom(15);
            } else {
                console.info('geocoding %s failed with status %d', address, status);
            }
        }
        /* invoke the geocoder service with your location to geocode */
        geocoder.geocode({
            'address': address
        }, evtcallback);
    }
</script>

<script>
    // quando a página é carregada, vai buscar a informação da cidade.
    function fetchCityData() {
        <?php
        echo "
            var localidade='$cidade';
            ";
        ?>

        $.ajax({
            url: "https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exlimit=max&explaintext&exintro&titles=" + localidade,
            method: "GET",
            dataType: "json",
            success: function(data) {
                var html = "";
                var pageID = parseInt(Object.keys(data.query.pages));
                var descricao = data.query.pages[pageID].extract;
                console.log(descricao);

                var div = document.getElementById('descr-city');

                div.innerHTML = "<p id='descr-cidade' style='font-size:14px'>" + descricao + "</p>";
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    function fetchCountryNews() {
        <?php
        echo "
            var pais='$pais';
            ";
        ?>

        $.ajax({
            url: "https://newsapi.org/v2/top-headlines?country=" + pais + "&apiKey=2b4ac2d5f2ab4b5f82f381e21089944b",
            method: "GET",
            dataType: "json",
            success: function(data) {
                var html = "";
                var noticias = data.articles;
                var newsDiv = document.getElementById('news');

                for (var i = 0; i < noticias.length; i++) {
                    html += `<div class="noticia">
                    <a href="${noticias[i]['url']}" target="_blank">
                    <img class="capaNoticia" src="${noticias[i]['urlToImage']}">
                    </a>
                    <p class="noticiasTitulo" style="font-weight:bold">${noticias[i]['title']}</p>
                    <p class="noticiaDesc">${noticias[i]['description']}</p>
                    </div>
                    `;
                }
                newsDiv.innerHTML = html;
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>

<script>
    // funcao para fazer o load de todas as informações quando a página é carregada.
    function loadData() {
        fetchCityData();
        fetchCountryNews();
    }
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJQOFRmF8nif---ccmh2KyGsSc-9DpR4s&callback=initMap">
</script>

</html>