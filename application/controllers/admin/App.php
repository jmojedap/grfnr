<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/app/';
    public $url_controller = URL_ADMIN . 'app/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función de la aplicación de administración
     */
    function index()
    {
        if ( $this->session->userdata('logged') )
        {
            $this->logged();
        } else {
            redirect('app/accounts/login');
        }    
    }

    function dashboard()
    {
        $data['summary'] = $this->App_model->summary();
        $data['head_title'] = APP_NAME;
        $data['view_a'] = $this->views_folder . 'dashboard_v';
        $this->App_model->view(TPL_ADMIN, $data);

        //$this->output->enable_profiler(TRUE);
    }

// HELP
//-----------------------------------------------------------------------------

    function help($post_id = 0)
    {
        $data['head_title'] = 'Ayuda';
        $data['view_a'] = $this->views_folder . 'help/help_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function test(){
        $data['head_title'] = 'Nueva';
        $data['view_a'] = 'templates/apml/content';
        $this->App_model->view('templates/admin_pml/main', $data);
    }

    function d3(){
        $data['head_title'] = 'D3JS';
        $data['view_a'] = 'app/app/d3js/testing';
        $this->App_model->view('templates/easypml/main', $data);
    }

// Especiales de la aplicación
//-----------------------------------------------------------------------------

    /**
     * Verificar si existe usuario participante, crearlo si no existe.
     * Iniciar sesión para participación en Grafinar
     * 2022-08-26
     */
    function start_session()
    {
        //Respuesta inicial
            $data['status'] = 0;
            $data['message'] = 'No se pudo iniciar la sesión de respuesta';
        
        //Validar Recaptcha
        $this->load->model('Validation_model');
        $data['recaptcha'] = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3
        
        if ( $data['recaptcha'] == 1 ) {
            $respondent_id = 0; //Valor inicial

            $username = $this->App_model->respondent_username();
            $user = $this->Db_model->row('users', "username = '{$username}'");
            if ( is_null($user) ) {
                //No existe, crear
                $respondent_id = $this->App_model->create_respondent($username);
            } else {
                $respondent_id = $user->id;
            }
    
            if ( $respondent_id > 0 ) {
                $this->load->model('Account_model');
                $this->Account_model->create_session($username);
                $data['status'] = 1;
                $data['message'] = 'Sesión iniciada';
            }
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}