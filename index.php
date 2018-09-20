<?php
  function loadCsv($path) {
      $csvFile = file($path);
      $csv = array_map('str_getcsv', $csvFile);
      array_walk($csv, function(&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
      });
      array_shift($csv); # remove column header
      return $csv;
  }
  // est72017sex0_age999
  // GEO.display-label

  // $censusData = loadCsv("")
  echo "Test";

?>
