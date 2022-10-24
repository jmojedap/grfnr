<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respuestas extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/grafinar/respuestas/';
    public $url_controller = URL_ADMIN . 'respuestas/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('Escena_model');
        $this->load->model('Respuesta_model');
        date_default_timezone_set("America/Bogota");    //Para definir hora local
    }
    
    function index($post_id = NULL)
    {
        if ( is_null($post_id) ) {
            redirect("admin/respuestas/explore/");
        } else {
            redirect("admin/respuestas/info/{$post_id}");
        }
    }

// EXPLORACIÓN
//-----------------------------------------------------------------------------

    /** Exploración de respuestas */
    function explore($num_page = 1)
    {
        //Identificar filtros de búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            //$data = $this->Post_model->explore_data($filters, $num_page, 60);
            $data = $this->Respuesta_model->get($filters, $num_page, 12);
            $data['numPage'] = $num_page;
            $data['cf'] = 'respuestas/explore/';
            $data['controller'] = 'respuestas/';
            $data['views_folder'] = $this->views_folder . 'explore/';      //Carpeta donde están las vistas de exploración
            $data['head_title'] = 'Respuestas';
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
            //unset($data['nav_2']);
        
        //Opciones de filtros de búsqueda
            $data['arrEscena'] = $this->App_model->options_post('type_id = 121');
            $data['arrInstitucion'] = $this->App_model->options_post('type_id = 101');
            $data['arrLevel'] = $this->Item_model->arr_options('category_id = 3 AND item_group = 1');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Listado de respuestas, filtrados por búsqueda, JSON
     */
    function get($num_page = 1, $per_page = 12)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data = $this->Respuesta_model->get($filters, $num_page, $per_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Eliminar un conjunto de posts seleccionados
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) 
        {
            $data['qty_deleted'] += $this->Respuesta_model->delete($row_id);
        }

        //Establecer resultado
        if ( $data['qty_deleted'] > 0 ) { $data['status'] = 1; }
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

        $data['query'] = $this->Respuesta_model->query_export($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'respuestas';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
        } else {
            $data = array('message' => 'No se encontraron respuestas para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

// PROCESO DE RESPUESTA
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Crea registro de respuesta de escena en la tabla posts (type 129)
     * 2022-09-02
     */
    function iniciar($escena_id)
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->Respuesta_model->iniciar($escena_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Guardar respuesta a una escena, enviada por participante
     * 2022-09-03
     */
    function guardar($escena_id, $respuesta_id)
    {
        $data = $this->Respuesta_model->guardar($escena_id, $respuesta_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Marcar un posts respuesta como finalizado
     * 2022-09-04
     */
    function finalizar($escena_id, $respuesta_id) {
        $data = $this->Respuesta_model->finalizar($escena_id, $respuesta_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    /**
     * Vista de respuesta de escena
     * 2022-08-26
     */
    function info($escena_id, $respuesta_id)
    {
        $data['escena'] = $this->Escena_model->row($escena_id);
        
        $data['head_title'] = $data['escena']->title;
        $data['view_a'] = $this->views_folder . 'info/info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';
        
        $data['respuesta'] = $this->Db_model->row_id('posts', $respuesta_id);
        $data['user'] = $this->Db_model->row_id('users', $data['respuesta']->related_1);
        $data['personajes'] = $this->Escena_model->personajes($escena_id);
        $data['arrGenero'] = $this->Item_model->arr_options('category_id = 59');
        $data['arrGrupoEdad'] = $this->Item_model->arr_options('category_id = 101');
        $data['arrEmocion'] = $this->Item_model->arr_options('category_id = 120');

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Actualizar los campos dependientes de las respuesas (t129) a escenas (t121)
     * 2022-10-01
     */
    function update_respuestas_fields()
    {
        $escenas = $this->db
        ->select('id, post_name')
        ->where('type_id', 121)
        ->get('posts');

        $qty_affected = 0;

        foreach ($escenas->result() as $escena) {
            $sql = "UPDATE posts
                SET text_1 = '{$escena->post_name}', post_name = 'Respuesta a: {$escena->post_name}'
                WHERE type_id = 129 AND related_2 = {$escena->id}";
            $this->db->query($sql);
            $qty_affected += $this->db->affected_rows();
        }

        $data['qty_affected'] = $qty_affected;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Actualizar detalles de emociones de personajes, tabla grf_answers
     * 2022-10-01
     */
    function update_detalles($format = '')
    {
        $this->db->select('id, related_2 AS escena_id');
        $this->db->where('type_id', 129);
        if ( $format != 'total' ) {
            $this->db->where('id NOT IN (SELECT answer_id FROM grf_answers)');
        }
        $respuestas = $this->db->get('posts');
        
        $qty_inserted = 0;
        $qty_respuestas = $respuestas->num_rows();

        foreach ($respuestas->result() as $respuesta) {
            $result = $this->Escena_model->guardar_detalle_respuesta($respuesta->escena_id, $respuesta->id);
            $qty_inserted += $result['qty_details'];
        }

        $data['qty_inserted'] = $qty_inserted;
        $data['qty_respuestas'] = $qty_respuestas;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function export_details()
    {
        set_time_limit(120);    //120 segundos, 2 minutos para el proceso

        //Identificar filtros y búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data['query'] = $this->Respuesta_model->query_export_details($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'respuestas_detalle';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
        } else {
            $data = array('message' => 'No se encontraron respuestas para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

// Procesamiento
//-----------------------------------------------------------------------------

    /**
     * HTML
     * Procesos de las respuestas
     * 2022-08-19
     */
    function processes()
    {
        $data['processes'] = file_get_contents(PATH_RESOURCES . "config/process_app.json");
    
        $data['head_title'] = 'Procesos de la aplicación';
        $data['view_a'] = $this->views_folder .  'processes_v';
        $data['nav_2'] = $this->views_folder .  'explore/menu_v';        
        $this->App_model->view(TPL_ADMIN, $data);
    }

// PALABRAS DE LAS NARRACIONES
//-----------------------------------------------------------------------------

    function words()
    {
        $data['head_title'] = 'Palabras más frecuentes';
        $data['view_a'] = $this->views_folder . 'words/words_v.php';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';

        $data['words'] =$this->Respuesta_model->words_frecuency();

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Actualizar tabla gfr_words, con las palabras de las narraciones de las respuestas
     * (post.content), type_id 129.
     * 2022-10-12
     */
    function update_answer_words()
    {
        $data = $this->Respuesta_model->update_answer_words();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Exportar palabras de las narraciones, tabla grf_words
     * 2022-10-17
     */
    function export_words()
    {
        set_time_limit(120);    //120 segundos, 2 minutos para el proceso

        //Identificar filtros y búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data['query'] = $this->Respuesta_model->words_query_export($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'palabras';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
        } else {
            $data = array('message' => 'No se encontraron palabras para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    /**
     * Exportar palabras de las narraciones, tabla grf_words, acumuladas por conteo
     * 2022-10-17
     */
    function export_words_accumulated()
    {
        set_time_limit(120);    //120 segundos, 2 minutos para el proceso

        $data['query'] = $this->Respuesta_model->words_accumulated_query_export();

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'frecuencia_palabras';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
        } else {
            $data = array('message' => 'No se encontraron palabras para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }
}