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
  $test = getPopulation($censusData);
  // $popFormat = '';
  // foreach ($test as $data) {
  //   $county = $data['county'];
  //   $county = chop($county, " , Pennsylvania") . "y";
  //   $pop = $data['pop'];
  //   if(empty($popFormat) == true) {
  //     $a = '[{"county":"' . $county . '","pop":' . $pop . '}';
  //   }
  //   else {
  //     $a = ',{"county":"' . $county . '","pop":' . $pop . '}';
  //   }
  //   $popFormat = $popFormat . $a;
  // }
  // $popFormat = $popFormat . ']';
  // var_dump($popFormat);
  // $county = [];
  $population = [];
  foreach ($test as $data) {
    $c = $data['county'];
    $c = chop($c, " , Pennsylvania") . "y";
    $pop = $data['pop'];

    // $population[] = '{"county":'.'"' . $c . '"'.',"pop":' . $pop . '}';

    // {"county":
    // $entry = "{'county':'";
    // // {"county":'name
    // $entry = $entry . $c;
    // // {'county':'name','pop':
    // $entry = $entry . "','pop':";
    // // {'county':'name','pop':population}
    // $entry = $entry . $pop . "}";

    // $entry = "{'" . $c . "':" . $pop . "}";
    $entry = $c . ":" . $pop;
    //
    $population[] = $entry;

    // $county[] = $c;
    // $population[] = $c;
    // $population[] = $data['pop'];
  }
  // var_dump($population);
  // $implodeCounty = implode(", ", $county);
  $implodePop = implode(",", $population);
  // var_dump($implodePop);

  setcookie('population', $implodePop);
  // setcookie('county', $implodeCounty);

  if(!isset($_COOKIE['population'])) {
      echo "Populatoin not set";
  } else {
      echo "Value is: " . $_COOKIE['population'];
  }
  // if(!isset($_COOKIE['county'])) {
  //     echo " County not set";
  // } else {
  //     echo "Value is: " . $_COOKIE['county'];
  // }
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
  <script src="//d3js.org/d3.v4.min.js"></script>
  <script>

  var getCookieData = () => {
    var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)population\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    var decodedCookie = decodeURIComponent(cookieValue);
    dataset = decodedCookie.replace(/\+/gi, " ");
    dataset = dataset.split(",");
    return dataset;
  }

  var dataset = getCookieData();

  var c;
  var ds = "";
  for(c = 0; c < dataset.length; c++){
    var test = dataset[c].split(":");
    var pop = "{'county':'" + test[0] + "','pop':" + test[1] + "}";
    if(ds  == ""){
      ds= "[" + pop;
    }
    else{
      ds = ds + "," + pop;
    }
  }
  ds = ds + "]"
  console.log(ds);
  // var data = "[{'county':'Town 1','pop':10000},{'county':'Town 2','pop':12345}]";

  var data = ds.split(",");
  // set the dimensions and margins of the graph
  var margin = {top: 10, right: 20, bottom: 30, left: 120},
      width = 2800 - margin.left - margin.right,
      height = 1000 - margin.top - margin.bottom;

  // set the ranges
  var y = d3.scaleBand()
            .range([height, 0])
            .padding(0.1);

  var x = d3.scaleLinear()
            .range([0, width]);

  // append the svg object to the body of the page
  // append a 'group' element to 'svg'
  // moves the 'group' element to the top left margin
  var svg = d3.select("body").append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");

    // format the data
    data.forEach(function(d) {
      d.pop = +d.pop;
    });

    // Scale the range of the data in the domains
    x.domain([0, d3.max(data, function(d){ return d.pop; })])
    y.domain(data.map(function(d) { return d.county; }));
    //y.domain([0, d3.max(data, function(d) { return d.sales; })]);

    // append the rectangles for the bar chart
    svg.selectAll(".bar")
        .data(data)
      .enter().append("rect")
        .attr("class", "bar")
        //.attr("x", function(d) { return x(d.sales); })
        .attr("width", function(d) {return x(d.pop); } )
        .attr("y", function(d) { return y(d.county); })
        .attr("height", y.bandwidth());

    // add the x Axis
    svg.append("g")
        .attr("transform", "translate(0," + height + ")")
        .call(d3.axisBottom(x));

    // add the y Axis
    svg.append("g")
        .call(d3.axisLeft(y));


  </script>
</body>
</html>
