<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Escenas extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/grafinar/escenas/';
    public $url_controller = URL_ADMIN . 'escenas/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('Escena_model');
        date_default_timezone_set("America/Bogota");    //Para definir hora local
    }
    
    function index($post_id = NULL)
    {
        if ( is_null($post_id) ) {
            redirect("admin/escenas/explore/");
        } else {
            redirect("admin/escenas/info/{$post_id}");
        }
    }

// EXPLORACIÓN
//-----------------------------------------------------------------------------

    /** Exploración de escenas */
    function explorar($num_page = 1)
    {
        //Identificar filtros de búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $filters['sf'] = 'general';  //Select format

        //Datos básicos de la exploración
            $data = $this->Post_model->explore_data($filters, $num_page, 60);
            $data['cf'] = 'escenas/explorar/';
            $data['controller'] = 'escenas/';
            $data['views_folder'] = $this->views_folder . 'explorar/';      //Carpeta donde están las vistas de exploración
            $data['head_title'] = 'escenas';
            $data['view_a'] = $data['views_folder'] . 'explore_v';
        
        //Opciones de filtros de búsqueda
            //$data['options_cat_1'] = $this->Item_model->options('category_id = 21 AND level = 0', 'Todos');
            $data['options_cat_1'] = $this->Post_model->options_cat_1();
            $data['options_clasificacion'] = $this->Post_model->options_clasificacion();
            
        //Arrays con valores para contenido en lista
            //$data['arr_cat'] = $this->Item_model->arr_cod('category_id = 21');
            
        //Cargar vista
            $this->App_model->view('templates/easypml/main', $data);
    }

    /**
     * Listado de escenas, filtrados por búsqueda, JSON
     */
    function get($num_page = 1, $per_page = 60)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $filters['sf'] = '121_escenas';  //Select format
        $filters['condition'] = 'type_id = 121';

        $data = $this->Post_model->get($filters, $num_page, $per_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Exportar resultados de búsqueda
     * 2021-09-27
     */
    function export()
    {
        set_time_limit(120);    //120 segundos, 2 minutos para el proceso

        //Identificar filtros y búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data['query'] = $this->Post_model->query_export($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'escenas';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
        } else {
            $data = array('message' => 'No se encontraron escenas para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }
    
// Especiales
//-----------------------------------------------------------------------------

    function personajes($escena_id)
    {
        $data = $this->Escena_model->basic($escena_id);
        $data['view_a'] = $this->views_folder . 'personajes_v';
        $data['nav_2'] = 'admin/posts/types/121/menu_v';

        $data['personajes'] = $this->Escena_model->personajes($escena_id);
        $data['arrGenero'] = $this->Item_model->arr_options('category_id = 59');
        $data['arrGrupoEdad'] = $this->Item_model->arr_options('category_id = 101');


        $this->App_model->view(TPL_ADMIN, $data);
    }

// PROCESO DE RESPUESTA
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Crea registro de respuesta de escena en la tabla posts (type 129)
     * 2022-09-02
     */
    function iniciar_respuesta($escena_id)
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->Escena_model->iniciar_respuesta($escena_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Guardar respuesta a una escena, enviada por participante
     * 2022-09-03
     */
    function guardar_respuesta($escena_id, $respuesta_id)
    {
        $data = $this->Escena_model->guardar_respuesta($escena_id, $respuesta_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Marcar un posts respuesta como finalizado
     * 2022-09-04
     */
    function finalizar_respuesta($escena_id, $respuesta_id) {
        $data = $this->Escena_model->finalizar_respuesta($escena_id, $respuesta_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}