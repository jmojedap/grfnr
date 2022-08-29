<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/config/';
    public $url_controller = URL_ADMIN . 'config/';


// Constructor
//-----------------------------------------------------------------------------
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Admin_model');
        
        //Para formato de horas
        date_default_timezone_set("America/Bogota");

    }
        
    function index()
    {
        $this->options();
    }
        
// SIS OPTION 2019-06-15
//---------------------------------------------------------------------------------------------------

    /**
     * Listas de documentos, creación, edición y eliminación de opciones
     */
    function options()
    {
        $data['head_title'] = 'Opciones del sistema';
        $data['nav_2'] = $this->views_folder . 'menu_v';        
        $data['view_a'] = $this->views_folder . 'options_v';        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX - JSON
     * Listado de las opciones de documentos (posts.type_id = 7022)
     */
    function get_options()
    {
        $data['options'] = $this->db->get('sis_option')->result();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX - JSON
     * Inserta o actualiza una opcione de documentos (posts.type_id = 7022)
     */
    function save_option($option_id = 0)
    {
        $option_id = $this->Admin_model->save_option($option_id);

        $data = array('status' => 0, 'message' => 'La opción no fue guardada');
        if ( ! is_null($option_id) ) { $data = array('status' => 1, 'message' => 'Opción guardada: ' . $option_id); }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Elimina una opcione de documentos, registro de la tabla post
     */
    function delete_option($option_id)
    {
        $data = $this->Admin_model->delete_option($option_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Colores
//-----------------------------------------------------------------------------

    /**
     * Conjunto de colores de la herramienta
     * 2020-03-18
     */
    function colors()
    {
        $data['head_title'] = 'Colores';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'colors_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

// Procesos
//-----------------------------------------------------------------------------

    /**
     * Procesos de la aplicación
     * 2022-08-19
     */
    function processes()
    {
        $data['processes'] = file_get_contents(PATH_RESOURCES . "config/process.json");
    
        $data['head_title'] = 'Procesos del sistema';
        $data['view_a'] = $this->views_folder .  'processes_v';
        $data['nav_2'] = $this->views_folder .  'menu_v';        
        $this->App_model->view(TPL_ADMIN, $data);
    }

// Importar Excel
//-----------------------------------------------------------------------------

    /**
     * Mostrar formulario de importación de datos a tabla de Excel
     * con archivo Excel. El resultado del formulario se envía a 
     * 'config/import_e'
     * 2022-06-14
     */
    function import($table_name = 'users')
    {
        //Variables específicas
            $data['destination_form'] = URL_ADMIN . 'config/import_e';
            $data['table_name'] = $table_name;
            $data['sheet_name'] = 'rows';
            $data['tables'] = $this->db->list_tables();
            
        //Variables generales
            $data['head_title'] = 'Importar de Excel';
            $data['view_a'] = 'admin/config/import_excel_v';
            $data['nav_2'] = $this->views_folder . 'menu_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Ejecuta (e) la importación de datos con archivo Excel
     * 2022-07-12
     */
    function import_e()
    {
        $this->load->model('Import_model');

        $table_name = $this->input->post('table_name');
        //Proceso
        $this->load->library('excel');            
        $imported_data = $this->excel->arr_sheet_default($this->input->post('sheet_name'));
        $data['columns'] = $this->excel->get_columns($this->input->post('sheet_name'));
        
        if ( $imported_data['status'] == 1 )
        {
            $data = $this->Import_model->import($table_name, $imported_data['arr_sheet'], $data['columns']);
        }

        //Cargue de variables
            $data['status'] = $imported_data['status'];
            $data['message'] = $imported_data['message'];
            $data['arr_sheet'] = $imported_data['arr_sheet'];
            $data['sheet_name'] = $this->input->post('sheet_name');
            $data['back_destination'] = "users/import/";
        
        //Cargar vista
            $data['head_title'] = 'Importar: ' . $table_name;
            $data['view_a'] = 'admin/config/import_excel_result_v';
            $data['nav_2'] = $this->views_folder . 'menu_v';

        $this->App_model->view(TPL_ADMIN, $data);

        //Salida JSON
        //$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }



// Pruebas y desarrollo
//-----------------------------------------------------------------------------

    /**
     * Reestablecer sistema para pruebas
     * 2019-07-19
     */
    function reset()
    {
        //IMPORT USERS
        $this->db->query('DELETE FROM users WHERE id != 200002;');

        //FOLLOWERS
        //$this->db->query('DELETE FROM users_meta WHERE type_id = 1011;');
        //$this->db->query('UPDATE users SET qty_followers = 0, qty_following = 0;');

        $data = array('status' => 1, 'message' => 'Listo');
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function test_email()
    {
        $this->load->model('Account_model');

        $users = $this->db->get('users');
        foreach ($users->result() as $user) {
            echo $user->email;
            echo ' --- ';
            echo $this->Account_model->email_to_username($user->email);
            echo '<br>';
        }

        $test_email = 'dfhsd00=)(/=(/**--467fsdfads7987fdsfds6497dfsdf99999d@gmail.com';

        echo $test_email;
        echo ' --- ';
        echo $this->Account_model->email_to_username($test_email);
        echo '<br>';
    }
}