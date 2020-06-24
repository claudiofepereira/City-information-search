<section class="container" id="container">
    <div class="info-meteo" id="info-meteo">
        <h1>Informação meteorológica para <?php echo "$cidade" ?></h1>
        <h3><?php echo "Temperatura: " . $temperatura . " graus" ?></h3>
        <h3><?php echo "Previsão temporal: " . $tempoAtual ?></h3>
        <h3><?php echo "Probabilidade de ocorrer precipitação: " . $precipProbability * 100 . "%" ?></h3>
        <h3><?php echo "Velocidade do vento: " . floor($windSpeed) . " m/s" ?></h3>
        <img src="<?php echo $iconUrl ?>">
    </div>

    <div id="map" class="map"></div>
</section>