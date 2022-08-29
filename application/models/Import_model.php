<?php
class Import_model extends CI_Model{

    /* 
     * Funciones para la importación masiva de datos a la DB
     * Actualizada 2022-07-12
     */

// IMPORTAR DATOS DESDE EXCEL
//-----------------------------------------------------------------------------

    /**
     * Importa registros a la base de datos desde un archivo Excel
     * 2022-06-14
     */
    function import($table_name, $arr_sheet, $columns)
    {
        //Resultado inicial por defecto
        $data = array(
            'qty_imported' => 0,
            'results' => array(),
            'table_name' => $table_name,
            'errors' => array()
        );

        //Información campos de la tabla
        $fields = $this->db->list_fields($table_name);
        $field_data = $this->field_data_import($table_name);

        //Verificar que columnas estén en la lista de campos de la tabla
        foreach ($columns as $column) {
            if ( ! in_array($column, $fields) ) {
                $data['errors'][] = "El campo '{$column}' no existe en la tabla '{$table_name}'";
            }
        }

        //Si no hay errores, importar cada registro
        if ( count($data['errors']) == 0 ) {
            foreach ( $arr_sheet as $key => $row_data )
            {
                $data_import = $this->import_row($table_name, $row_data, $columns, $field_data);
                $data['qty_imported'] += $data_import['status'];
                $data['results'][$key + 2] = $data_import;
            }
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel.
     * 2022-06-27
     */
    function import_row($table_name, $row_data, $columns, $field_data)
    {
        //Validar
            $error_text = '';
            //if (  ) { $error_text .= 'El nombre está vacío. '; }          //Debe tener nombre escrito

        //Si no hay error
            if ( $error_text == '' )
            {
                foreach ($columns as $key => $column) {
                    $arr_row[$column] = $this->import_field_value($row_data[$key], $field_data[$column]['type']);
                }

                //Guardar en la tabla
                $condition = 'id = 0';
                if ( isset($arr_row['id']) ) {
                    if ( $arr_row['id'] > 0 ) {
                        $condition = "id = {$arr_row['id']}";
                    }
                }
                $saved_id = $this->Db_model->save($table_name, $condition, $arr_row);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $saved_id);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

    /**
     * Devuelve el valor para de un campo para el proceso de importación desde
     * excel, a partir del valor encontrado en el archivo y el tipo de campo
     * de la tabla.
     * 2022-07-05
     */
    function import_field_value($row_value, $field_type)
    {
        //Valor por defecto
        $value = 0; //Si el campo es numérico

        //Si es texto o cadena:
        if ( in_array($field_type, array('varchar','text')) )
        { $value = ''; }

        //Establecer valor
        if ( strlen($row_value) > 0 ) {
            //Si el valor de la casilla no es nulo, se toma ese valor
            $value = $row_value;

            //Si es fecha:
            if ( in_array($field_type, array('datetime','date')) )
            { $value = $this->pml->dexcel_dmysql($row_value); }
        }

        return $value;
    }

    /**
     * Datos de los campos de una tabla, referencia para importación
     * 2022-06-15
     */
    function field_data_import($table_name): array
    {
        $fields = array();

        $field_data = $this->db->field_data($table_name);
        foreach ($field_data as $field)
        {
            $fields[$field->name] = (array)$field;
        }

        return $fields;
    }
}