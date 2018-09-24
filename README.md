Data Analysis Lab:

  Discover/Requirements:
  For this lab I am going to create a visualization of the popular of the different counties in PA, using the CSV file from the census website.

  Collection:
  Data got from the census website: https://www.census.gov/data/datasets/2017/demo/popest/counties-detail.html

  Data Prep/Cleaning:
  Looking through the CSV file, I've determined the two columns I will use are the: GEO.display-label (county names) and est72017sex0_age999 (newest record of population for both sexes).

  Exploration/Planning:
  After I extracted the data from the CSV file I found everything to be there, nothing missing. As for the visualization, I decided with the population data to either create a bar graph or a bubble chart to show each county's different population.

  Modeling/Algorithm:
  With the introduction of passing the data with a php cookie, I found creating a bar graph much more challenging then I thought. This resulted in me going with a bubble chart, which I found a usable example at: https://bl.ocks.org/mbostock/4063269

  Automation/Computation:
  This is the meat of the work. Gathering the data from the CSV file, I formatted it and sent it to the Javascript with a php cookie, instead of using echo within the Javascript. Once in the Javascript, I had to parse the data and create an array that would be usable with the bubble chart I was attempting to create.

  Findings/Review/Repeat:
  The bubble chart is a success, only minor formatting was needed to get it to my liking. If time permits, I will attempt to expand the chart to include multiple years for the populations.
