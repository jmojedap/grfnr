<?php $this->load->view('assets/highcharts') ?>
<script src="https://code.highcharts.com/highcharts-more.js"></script>

<div class="row">
    <div class="col-md-4">
        <div class="mb-2">
            <a href="<?= URL_API . "respuestas/export_words" ?>" class="btn btn-light">
                <i class="fa fa-download"></i> Palabras detalle
            </a>
            <a href="<?= URL_API . "respuestas/export_words_accumulated" ?>" class="btn btn-light">
                <i class="fa fa-download"></i> Palabras acumuladas
            </a>
        </div>
        <div id="wordsApp">
            <table class="table bg-white">
                <thead>
                    <th>No.</th>
                    <th>Palabra</th>
                    <th>Veces usada</th>
                </thead>
                <tbody>
                    <tr v-for="(word, key) in words">
                        <td>{{ key + 1 }}</td>
                        <td>{{ word.name }}</td>
                        <td>{{ word.value }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <figure class="highcharts-figure">
            <div id="chart" style="" class="border"></div>
        </figure>
    </div>
</div>

<?php $this->load->view('admin/grafinar/respuestas/words/script_v.php') ?>