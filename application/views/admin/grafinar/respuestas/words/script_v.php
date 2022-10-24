<?php
    $arrWords = [];
    foreach ($words->result() as $row_word) {
        $word['name'] = $row_word->word;
        $word['value'] = intval($row_word->qty_use);
        $arrWords[] = $word;
    }
?>

<script>

var arrWords = <?= json_encode($arrWords) ?>;

Highcharts.theme = hc_grafinar_theme;
Highcharts.setOptions(Highcharts.theme);

Highcharts.chart('chart', {
    chart: {
        type: 'packedbubble',
        height: '700px',
        renderTo: "chart",
    },
    title: {
        text: 'Palabras más usadas en las narraciones'
    },
    tooltip: {
        useHTML: true,
        pointFormat: '<b>{point.name}:</b> {point.value}'
    },
    plotOptions: {
        packedbubble: {
            minSize: '30%',
            maxSize: '120%',
            zMin: 0,
            zMax: 1000,
            layoutAlgorithm: {
                splitSeries: false,
                gravitationalConstant: 0.02
            },
            dataLabels: {
                enabled: true,
                format: '{point.name}',
                filter: {
                    property: 'y',
                    operator: '>',
                    value: 250
                },
                style: {
                    color: 'black',
                    textOutline: 'none',
                    fontWeight: 'normal'
                }
            }
        }
    },
    series: [
        {
            name: 'Palabras',
            data: arrWords,
        }
    ]
});

var wordsApp = new Vue({
    el: '#wordsApp',
    created: function(){
        //this.get_list()
    },
    data: {
        words: <?= json_encode($arrWords) ?>,
        loading: false,
    },
    methods: {
        
    }
})
</script>