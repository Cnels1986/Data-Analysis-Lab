<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<?php

  function loadCsv($path) {
      $csvFile = file($path);
      $csv = array_map('str_getcsv', $csvFile);
      array_walk($csv, function(&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
      });
      array_shift($csv); # remove column header
      array_shift($csv);
      return $csv;
  }

  function getPopulation($data) {
    $population = [];
    $a = 0;
    forEach ($data as $dataPoint) {
      $name = $dataPoint['GEO.display-label'];
      $pop= intval($dataPoint['est72017sex0_age999']);
      $population[$a] = [
        'county'=> $name,
        'pop' => $pop];
      $a++;
    }
    return $population;
  }

  $censusData = loadCsv('http://localhost/dataAnalysis/pacensus.csv');
  // var_dump($censusData);
  $test = getPopulation($censusData);
  var_dump($test);
?>
