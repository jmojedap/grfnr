<div class="center_box_750">
    <div class="card mb-2">
        <div class="card-body">
            <form action="<?= $destination_form ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div class="mb-3 row">
                    <label for="file" class="col-md-3 col-form-label text-right">Archivo</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="table_name" class="col-md-3 col-form-label text-right">Tabla</label>
                    <div class="col-md-9">
                        <select name="table_name" class="form-control" required>
                            <?php foreach ( $tables as $option_table ) : ?>
                                <option value="<?= $option_table ?>"><?= $option_table ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="sheet_name" class="col-md-3 col-form-label text-right">Hoja de cálculo</label>
                    <div class="col-md-9">
                        <input
                            type="text" name="sheet_name" class="form-control"
                            placeholder="Nombre de la hoja de cálculo" title="Nombre de la hoja de cálculo"
                            required value="<?= $sheet_name ?>"
                            >
                    </div>
                </div>
            
                <div class="mb-3 row">
                    <div class="col-md-9 offset-md-3">
                        <button class="btn btn-primary w120p">Importar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>¿En qué consiste este proceso?</h5>
            <p>Se importarán datos a la base de datos.</p>

            <h5>Instrucciones para importar datos con archivo Excel</h5>
            <ul>
                <li>El tipo de archivo requerido es <b class="text-success">Excel (.xlsx)</b>.</li>
                <li>Verifique que el primer registro esté ubicado en la <b class="text-success">fila 2</b> de la hoja de cálculo.</li>
            </ul>
        </div>
    </div>
</div>