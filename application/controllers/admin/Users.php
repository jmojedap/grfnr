<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/users/';
    public $url_controller = URL_ADMIN . 'users/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {        
        parent::__construct();

        $this->load->model('User_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($user_id)
    {
        redirect("admin/users/profile/{$user_id}");
    }
    
//EXPLORE
//---------------------------------------------------------------------------------------------------
            
    /**
     * Exploración y búsqueda de usuarios
     * 2022-08-19
     */
    function explore($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->User_model->explore_data($filters, $num_page);
            
        //Arrays con valores para contenido en lista
            $data['arr_role'] = $this->Item_model->arr_options('category_id = 58');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Listado de users, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->User_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Eliminar un conjunto de users seleccionados
     * 2021-02-20
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) {
            $data['qty_deleted'] += $this->User_model->delete($row_id);
        }
        
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

        $data['query'] = $this->User_model->query_export($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'users';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
            //Salida JSON
            //$this->output->set_content_type('application/json')->set_output(json_encode($file_data['obj_writer']));
        } else {
            $data = array('message' => 'No se encontraron registros para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

// DATOS
//-----------------------------------------------------------------------------
    /**
     * Información general del usuario
     */
    function profile($user_id)
    {        
        //Datos básicos
        $data = $this->User_model->basic($user_id);

        $data['view_a'] = $this->views_folder . 'profile/profile_v';

        if ( $data['row']->role == 21 ) {
            $data['view_a'] = $this->views_folder . 'profile/21_v';
        }

        $data['back_link'] = $this->url_controller . 'explore';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Información de un usuario específico
     * 2022-01-05
     */
    function get_info($user_id)
    {
        $data['user'] = $this->User_model->get_info($user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
// CRUD
//-----------------------------------------------------------------------------

    /**
     * Formulario para la creación de un nuevo usuario
     * 2021-12-07
     */
    function add($role_type = 'deportista')
    {
        //Variables específicas
            $data['role_type'] = $role_type;

        //Opciones Select
            $data['options_role'] = $this->Item_model->options("category_id = 58 AND cod > {$this->session->userdata('role')}");
            $data['options_gender'] = $this->Item_model->options('category_id = 59 AND cod <= 2', 'Sexo');

        //Variables generales
            $data['head_title'] = 'Agregar usuario';
            $data['nav_2'] = 'admin/users/explore/menu_v';
            $data['view_a'] = 'admin/users/add/general/add_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Se validan los datos de un user add o existente ($user_id), los datos deben cumplir varios criterios
     * 2021-02-02
     */
    function validate($user_id = NULL)
    {
        $data = $this->User_model->validate($user_id);
        
        //Enviar resultado de validación
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Guardar datos de un usuario, insertar o actualizar
     * 2022-08-03
     */
    function save()
    {
        $validation = $this->User_model->validate($this->input->post('id'));
        
        if ( $validation['status'] == 1 )
        {
            $arr_row = $this->User_model->arr_row();
            $data['saved_id'] = $this->User_model->save($arr_row);
        } else {
            $data = $validation;
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
// EDICIÓN Y ACTUALIZACIÓN
//-----------------------------------------------------------------------------

    /**
     * Formulario para la edición de los datos de un user. Los datos que se
     * editan dependen de la $section elegida.
     * 2021-01-05
     */
    function edit($user_id, $section = 'basic')
    {
        //Datos básicos
        $data = $this->User_model->basic($user_id);

        //Opciones Select
        $data['options_role'] = $this->Item_model->options("category_id = 58 AND cod > {$this->session->userdata('role')}");
        $data['options_gender'] = $this->Item_model->options('category_id = 59 AND cod <= 2', 'Género');
        $data['options_city'] = $this->App_model->options_place('type_id = 4', 'cr', 'Ciudad');
        $data['options_document_type'] = $this->Item_model->options('category_id = 53', 'Tipo documento');
        
        $view_a = $this->views_folder . "edit/{$section}_v";
        if ( $section == 'cropping' )
        {
            $view_a = 'admin/files/cropping_v';
            $data['image_id'] = $data['row']->image_id;
            $data['url_image'] = $data['row']->url_image;
            $data['back_destination'] = "users/edit/{$user_id}/image";
        }
        
        //Array data espefícicas
            $data['back_link'] = $this->url_controller . 'explore';
            $data['nav_3'] = $this->views_folder . 'edit/menu_v';
            $data['view_a'] = $view_a;
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Actualiza el campo user.activation_key, para activar o restaurar la contraseña de un usuario
     * 2019-11-18
     */
    function set_activation_key($user_id)
    {
        $this->load->model('Account_model');
        $activation_key = $this->Account_model->activation_key($user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($activation_key));
    }
    
// IMAGEN DE PERFIL DE USUARIO
//-----------------------------------------------------------------------------
    /**
     * AJAX JSON
     * Carga file de image y se la asigna a un user.
     * 2020-02-22
     */
    function set_image($user_id)
    {
        //Cargue
        $this->load->model('File_model');
        $data_upload = $this->File_model->upload($this->session->userdata('user_id'));
        
        $data = $data_upload;
        if ( $data_upload['status'] )
        {
            $this->User_model->remove_image($user_id);                                  //Quitar image actual, si tiene una
            $data = $this->User_model->set_image($user_id, $data_upload['row']->id);    //Asignar imagen nueva
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * POST REDIRECT
     * 
     * Proviene de la herramienta de recorte users/edit/$user_id/crop, 
     * utiliza los datos del form para hacer el recorte de la image.
     * Actualiza las miniaturas
     * 
     * @param type $user_id
     * @param type $file_id
     */
    function crop_image_e($user_id, $file_id)
    {
        $this->load->model('File_model');
        $this->File_model->crop($file_id);
        redirect("users/edit/{$user_id}/image");
    }
    
    /**
     * AJAX
     * Desasigna y elimina la image asociada a un user, si la tiene.
     * 
     * @param type $user_id
     */
    function remove_image($user_id)
    {
        $data = $this->User_model->remove_image($user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// IMPORTACIÓN DE USUARIOS
//-----------------------------------------------------------------------------

    /**
     * Mostrar formulario de importación de usuarios
     * con archivo Excel. El resultado del formulario se envía a 
     * 'users/import_e'
     */
    function import()
    {
        //Iniciales
            $data['help_note'] = 'Se importarán usuarios a la herramienta.';
            $data['help_tips'] = array(
                'La contraseña debe tener al menos 8 caracteres'
            );
        
        //Variables específicas
            $data['destination_form'] = "admin/users/import_e";
            $data['template_file_name'] = 'f01_usuarios.xlsx';
            $data['sheet_name'] = 'usuarios';
            $data['url_file'] = URL_RESOURCES . 'import_templates/' . $data['template_file_name'];
            
        //Variables generales
            $data['head_title'] = 'Usuarios';
            $data['view_a'] = 'common/import_v';
            $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Ejecuta (e) la importación de usuarios con archivo Excel
     * 2019-09-20
     */
    function import_e()
    {
        //Proceso
        $this->load->library('excel');            
        $imported_data = $this->excel->arr_sheet_default($this->input->post('sheet_name'));
        
        if ( $imported_data['status'] == 1 )
        {
            $data = $this->User_model->import($imported_data['arr_sheet']);
        }

        //Cargue de variables
            $data['status'] = $imported_data['status'];
            $data['message'] = $imported_data['message'];
            $data['arr_sheet'] = $imported_data['arr_sheet'];
            $data['sheet_name'] = $this->input->post('sheet_name');
            $data['back_destination'] = "users/import/";
        
        //Cargar vista
            $data['head_title'] = 'Usuarios';
            $data['view_a'] = 'common/import_result_v';
            $data['nav_2'] = $this->views_folder . 'explore/menu_v';

        $this->App_model->view(TPL_ADMIN, $data);
        //Salida JSON
        //$this->output->set_content_type('application/json')->set_output(json_encode($imported_data));
    }
    
//---------------------------------------------------------------------------------------------------
    
    /**
     * AJAX
     * Devuelve un valor de username sugerido disponible,
     * a partir de un email
     */
    function username()
    {
        $email = $this->input->post('email');
        $username = $this->User_model->email_to_username($email);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($username));
    }

// LISTAS
//-----------------------------------------------------------------------------

    /**
     * Vista listas de usuarios, permite editar las listas a las que un usuario
     * pertenece.
     * 2021-12-03
     */
    function lists($user_id)
    {
        //$data = $this->UY->basic();
        $data = $this->User_model->basic($user_id);

        //Obtener listas
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $filters['sf'] = '22_list';
        $filters['condition'] = 'type_id = 22 AND cat_1 = 10';
        
        $this->load->model('Post_model');
        $data['user_lists'] = $this->User_model->lists($user_id);

        $data['head_title'] = 'Listas de usuarios';
        $data['view_a'] = $this->views_folder . 'lists/lists_v';
        $data['back_link'] = $this->url_controller . 'explore';
        //$data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function update_list()
    {
        $user_id = $this->input->post('user_id');
        $list_id = $this->input->post('list_id');
        $add = $this->input->post('add');
        
        $data = $this->User_model->update_list($user_id, $list_id, $add);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}