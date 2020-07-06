
$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var chvarobj = {
    
    };
    
    
    var doughnutPieData = {
        datasets: [{
          data: [
                    genreresultset[0][0].item_count,
                    genreresultset[0][1].item_count,
                    genreresultset[0][2].item_count
                ],
          backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)'
          ],
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
        }],
    
        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
            genreresultset[0][0].name,
            genreresultset[0][1].name,
            genreresultset[0][2].name,
        ]
      };
      var doughnutPieOptions = {
        responsive: true,
        animation: {
          animateScale: true,
          animateRotate: true
        },
        legend: {
            labels: {
                fontColor: "white",
            }
        },
      };

    if ($("#playlistGenresChart").length) {
        chvarobj['pieChartCanvas'] = $("#playlistGenresChart").get(0).getContext("2d");
        chvarobj['pieChart'] = new Chart(chvarobj['pieChartCanvas'], {
          type: 'pie',
          data: doughnutPieData,
          options: doughnutPieOptions
        });
        


        setInterval(function() { 
            
            changeChart('pieChart','pieChartCanvas',doughnutPieData,doughnutPieOptions,'playlistGenresChart');

         }, 15000);


        function changeChart(chartvariable,chartcanvas,chartdata,chartoptions,chartid)
        {

            if(chvarobj[chartvariable]!==undefined && chvarobj[chartvariable]!='')
            {
            // console.log(chvarobj[chartvariable]);
            
            chvarobj[chartvariable].destroy();

            let nextid=0;
            let currentid = $('#'+chartid).data('currentchartid');
            if(currentid==19)
            nextid=0;
            else
            nextid=currentid+1;


           
            var chartdata = {
                datasets: [{
                  data: [
                            genreresultset[nextid][0].item_count,
                            genreresultset[nextid][1].item_count,
                            genreresultset[nextid][2].item_count
                        ],
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
                  ],
                  borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                  ],
                }],
            
                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    genreresultset[nextid][0].name,
                    genreresultset[nextid][1].name,
                    genreresultset[nextid][2].name,
                ]
              };

            chvarobj[chartvariable] = new Chart(chvarobj[chartcanvas], {
                    type: 'pie',
                    data: chartdata,
                    options: chartoptions
                });

                

                $('#'+chartid).data('currentchartid',nextid);

            }

            
            

        }

      }




});