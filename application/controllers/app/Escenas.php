<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Escenas extends CI_Controller {
        
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
        $this->load->model('Respuesta_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función
     */
    function index()
    {
        $this->catalogo();
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

        $this->App_model->view('templates/easypml/answers', $data);
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
     * Vista de respuesta de escena
     * 2022-08-26
     */
    function responder($escena_id, $respuesta_id)
    {
        $data['escena'] = $this->Escena_model->row($escena_id);

        $data['head_title'] = $data['escena']->title;
        $data['view_a'] = $this->views_folder . 'responder/responder_v';

        $data['respuesta'] = $this->Db_model->row_id('posts', $respuesta_id);
        $data['personajes'] = $this->Escena_model->personajes($escena_id);
        $data['arrGenero'] = $this->Item_model->arr_options('category_id = 59');
        $data['arrGrupoEdad'] = $this->Item_model->arr_options('category_id = 101');
        $data['arrEmocion'] = $this->Item_model->arr_options('category_id = 120');

        $this->App_model->view('templates/easypml/answers', $data);
    }

    /**
     * AJAX JSON
     * Guardar respuesta a una escena, enviada por participante
     * 2022-09-03
     */
    function guardar_respuesta($escena_id, $respuesta_id)
    {
        $data = $this->Respuesta_model->guardar($escena_id, $respuesta_id);

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
}