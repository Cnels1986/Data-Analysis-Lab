<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<?php

  // function loads the csv file
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
      // Selects from the 2 columns I want the data from, county name and the newest population totals
      $name = $dataPoint['GEO.display-label'];
      $pop= intval($dataPoint['est72017sex0_age999']);
      $population[$a] = [
        'county'=> $name,
        'pop' => $pop];
      $a++;
    }
    // Returns an array of the data
    return $population;
  }

  $censusData = loadCsv('http://localhost/dataAnalysis/pacensus.csv');
  $test = getPopulation($censusData);

  // constructs an array and formats the data needed to be sent through the cookie
  $population = [];
  foreach ($test as $data) {
    $c = $data['county'];
    $c = chop($c, " , Pennsylvania") . "y";
    $pop = $data['pop'];

    $entry = $c . ":" . $pop;
    $population[] = $entry;
  }
  $implodePop = implode(",", $population);
  setcookie('population', $implodePop);

  unset($_COOKIE['population']);
  setcookie('population', NULL, -1, '/');
?>

<!DOCTYPE html>
  <meta charset="utf-8">
  <style>
    .bar { fill: steelblue; }
  </style>

<html>
<body>
  <h1>Pennsylvania County Population</h1>
  <svg width="1920" height="1920" font-family="sans-serif" font-size="10" text-anchor="middle"></svg>
  <script src="//d3js.org/d3.v4.min.js"></script>
  <script>

  // gets the data sent from the php cookie
  var getCookieData = () => {
    var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)population\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    var decodedCookie = decodeURIComponent(cookieValue);
    dataset = decodedCookie.replace(/\+/gi, " ");
    dataset = dataset.split(",");
    return dataset;
  }

  var dataset = getCookieData();

  var c;
  var ds = [];
  // loop formats the data from the cookie so it can be used to create the bubble chart
  for(c = 0; c < dataset.length; c++) {
    var test = dataset[c].split(":");
    var county = test[0];
    county = county.replace(" County", "");
    var population = test[1];
    var thingy = {id: county, value: population};
    ds.push(thingy);
  }
  console. log(ds);


  // code to create the actual bubble chart and populate it with the php cookie data that's been formatted correctly
  var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height");

  var format = d3.format(",d");

  var color = d3.scaleOrdinal(d3.schemeCategory20c);

  var pack = d3.pack()
  .size([width, height])
  .padding(1.5);

  var root = d3.hierarchy({children: ds})
  .sum(function(d) { return d.value; })
  .each(function(d) {
    if (id = d.data.id) {
      var id, i = id.lastIndexOf(".");
      d.id = id;
      d.package = id.slice(0, i);
      d.class = id.slice(i + 1);
    }
  });

  var node = svg.selectAll(".node")
  .data(pack(root).leaves())
  .enter().append("g")
  .attr("class", "node")
  .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

  node.append("circle")
  .attr("id", function(d) { return d.id; })
  .attr("r", function(d) { return d.r; })
  .style("fill", function(d) { return color(d.package); });

  node.append("clipPath")
  .attr("id", function(d) { return "clip-" + d.id; })
  .append("use")
  .attr("xlink:href", function(d) { return "#" + d.id; });

  node.append("text")
  .attr("clip-path", function(d) { return "url(#clip-" + d.id + ")"; })
  .selectAll("tspan")
  .data(function(d) { return d.class.split(/(?=[A-Z][^A-Z])/g); })
  .enter().append("tspan")
  .attr("x", 0)
  .attr("y", function(d, i, nodes) { return 13 + (i - nodes.length / 2 - 0.5) * 10; })
  .text(function(d) { return d; });

  node.append("title")
  .text(function(d) { return d.id + "\n" + format(d.value); });
  </script>
</body>
</html>
