<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class escenas extends CI_Controller {
        
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/escenas/';
    public $url_controller = URL_APP . 'escenas/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();

        $this->load->model('Post_model');
        $this->load->model('Escena_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función
     */
    function index()
    {
        $this->explorar();
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

// FUNCIONES HERRAMIENTA
//-----------------------------------------------------------------------------

    /**
     * Catálogo de las escenas que están disponibles para responder
     * 2022-08-26
     */
    function catalogo()
    {
        $data['head_title'] = APP_NAME;
        $data['view_a'] = $this->views_folder . 'catalogo_v';

        $this->App_model->view(TPL_FRONT, $data);
    }

    /**
     * Vista de respuesta de escena
     * 2022-08-26
     */
    function responder($post_id)
    {
        $data = $this->Escena_model->basic($post_id);
        $data['view_a'] = $this->views_folder . 'responder/responder_v';

        $this->App_model->view(TPL_FRONT, $data);

    }

    /**
     * Form login de users se ingresa con nombre de user y 
     * contraseña. Los datos se envían vía ajax a accounts/validate_login
     */
    function inicio()
    {        
        redirect('app/escenas/explorar');
    }

    /**
     * AJAX JSON
     * 
     * Recibe los datos POST del form en accounts/signup. Si se validan los 
     * datos, se registra el user. Se devuelve $data, con resultados de registro
     * o de validación (si falló).
     * 2021-04-15
     */
    function crear_sesion()
    {
        $data = array('status' => 0, 'message' => 'Datos no válidos');  //Initial result values
        
        $this->load->model('Validation_model');
        $data['recaptcha'] = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3
            
        if ( $data['recaptcha'] == 1 )
        {
            //Construir registro del nuevo user
                $userdata['username'] = $this->input->post('username');
                $this->session->set_userdata($userdata);
                $data['status'] = 1;
                $data['message'] = 'Sesión iniciada';
        } else {
            $data['message'] = 'Recaptcha no válido';
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function salir()
    {
        $this->session->sess_destroy();
        redirect('app/escenas/inicio/');
    }

// FUNCIONES DE REVISIÓN
//-----------------------------------------------------------------------------

    /**
     * REDIRECT
     * Ir a la siguiente escena aleatoria sin revisión
     * 2022-07-14
     */
    function siguiente($aleatorio_actual)
    {
        $escena_id = 2906;
        $next_aleatorio = 1;

        $this->db->select('id, aleatorio');
        $this->db->where('status', 0);
        $this->db->where('aleatorio >', $aleatorio_actual);
        $this->db->order_by('aleatorio', 'ASC');
        $escenas = $this->db->get('escenas',1);

        if ( $escenas->num_rows() > 0 ) {
            $escena_id = $escenas->row()->id;
            $next_aleatorio = $escenas->row()->aleatorio;
        }
        
        redirect("app/escenas/clasificar/{$escena_id}/{$next_aleatorio}");
    }

    /**
     * Vista Lectura de un contenido
     * 2022-08-17
     */
    function clasificar($escena_id = 2906, $aleatorio = 1)
    {
        $escena = $this->Db_model->row('escenas', "id = {$escena_id} AND aleatorio = {$aleatorio}");
        $data = $this->Post_model->basic($escena->id);
        $data['head_title'] = 'Revisar escena';

        $username = $this->session->userdata('username');
        
        $data['qty_user_checked'] = $this->Db_model->num_rows('escenas', "actualizado_por = '{$username}'");

        $data['options_cat_1'] = $this->Post_model->options_cat_1();
        $data['options_clasificacion'] = $this->Post_model->options_clasificacion();
        
        $data['head_title'] = 'Clasificar';
        $data['view_a'] = $this->views_folder . 'clasificar/clasificar_v';
        $data['nav_2'] = $this->views_folder . 'explorar/menu_v';

        $this->App_model->view(TPL_FRONT, $data);
    }

    /**
     * AJAX JSON
     * Guardar los datos de clafificación de la escena
     * 2022-08-19
     */
    function actualizar($escena_id)
    {
        $arr_row['actualizado_por'] = $this->session->userdata('username');
        $arr_row['status'] = 1; //Clasificado
        $arr_row['clasificacion'] = $this->input->post('clasificacion');
        $arr_row['cat_1'] = $this->input->post('cat_1');
        $arr_row['compartible'] = $this->input->post('compartible');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        $data['saved_id'] = $this->Db_model->save('escenas', "id = {$escena_id}", $arr_row);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
}