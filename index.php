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


  $popFormat = '';
  foreach ($test as $data) {
    $county = $data['county'];
    $county = chop($county, " , Pennsylvania") . "y";
    $pop = $data['pop'];
    if(empty($popFormat) == true) {
      $a = '[{"county":"' . $county . '","pop":' . $pop . '}';
    }
    else {
      $a = ',{"county":"' . $county . '","pop":' . $pop . '}';
    }
    $popFormat = $popFormat . $a;
  }
  $popFormat = $popFormat . ']';
  // var_dump($popFormat);
?>



<!DOCTYPE html>
<meta charset="utf-8">
<style>

.bar { fill: steelblue; }

</style>
<body>
<script src="//d3js.org/d3.v4.min.js"></script>
<script>
// var data = [{"salesperson":"Bob","sales":33},{"salesperson":"Robin","sales":12},{"salesperson":"Anne","sales":41},{"salesperson":"Mark","sales":16},{"salesperson":"Joe","sales":59},{"salesperson":"Eve","sales":38},{"salesperson":"Karen","sales":21},{"salesperson":"Kirsty","sales":25},{"salesperson":"Chris","sales":30},{"salesperson":"Lisa","sales":47},{"salesperson":"Tom","sales":5},{"salesperson":"Stacy","sales":20},{"salesperson":"Charles","sales":13},{"salesperson":"Mary","sales":29}];
// var data = [{"county":"Town 1","pop":10000},{"county":"Town 2","pop":12345}];
var data = <?php echo $popFormat; ?>;
// set the dimensions and margins of the graph
var margin = {top: 20, right: 20, bottom: 30, left: 140},
    width = 960 - margin.left - margin.right,
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
