<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/accounts/';
    public $url_controller = URL_APP . 'accounts/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();
        
        $this->load->model('Account_model');
        $this->load->model('User_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función de la aplicación
     */
    function index()
    {
        if ( $this->session->userdata('logged') )
        {
            redirect('app/accounts/logged');
        } else {
            redirect('app/accounts/login');
        }    
    }
    
//LOGIN
//---------------------------------------------------------------------------------------------------
    
    /**
     * Form login de users se ingresa con nombre de user y 
     * contraseña. Los datos se envían vía ajax a accounts/validate_login
     */
    function login()
    {        
        //Verificar si está logueado
            if ( $this->session->userdata('logged') )
            {
                redirect('app/accounts/logged');
            } else {
                $data['head_title'] = APP_NAME;
                $data['view_a'] = $this->views_folder . 'login_v';
                //$data['g_client'] = $this->Account_model->g_client(); //Para botón login con Google
                $this->load->view('templates/easypml/start', $data);
                //$this->load->view('templates/admin_pml/start', $data);
            }
    }
    
    /**
     * Destroy session and redirect to login, start.
     */
    function logout()
    {
        $this->Account_model->logout();
        redirect('app/accounts/login');
    }

    /**
     * Destinos a los que se redirige después de validar el login de usuario
     * según el rol de usuario (índice del array)
     * 2021-06-08
     */
    function logged()
    {
        $destination = 'app/accounts/login';
        if ( $this->session->userdata('logged') )
        {
            $arr_destination = array(
                1 => 'admin/app/dashboard/',  //Desarrollador
                2 => 'admin/app/dashboard/',  //Administrador
                4 => 'admin/calendar/calendar',   //Entrenador
                21 => 'app/accounts/profile/'     //Deportista
            );
                
            $destination = $arr_destination[$this->session->userdata('role')];
        }
        
        redirect($destination);
    }

// Magic Link
//-----------------------------------------------------------------------------

    /**
     * Form login de users se ingresa con nombre de user y 
     * contraseña. Los datos se envían vía ajax a accounts/validate_login
     * $activation_key, existe cuando es redireccionado por no superar validación
     * 2022-08-08
     */
    function login_link($activation_key = '')
    {        
        //Verificar si está logueado
            if ( $this->session->userdata('logged') )
            {
                redirect('app/accounts/logged');
            } else {
                $data['head_title'] = APP_NAME;
                $data['view_a'] = $this->views_folder . 'login_link_v';
                $data['activation_key'] = $activation_key;
                $this->load->view('templates/easypml/start', $data);
            }
    }

    /**
     * REDIRECT
     * Validar un activation_key, para login de usuario, si es válido
     * iniciar sesión, si no redireccionar.
     * 2022-08-08
     */
    function validate_login_link($activation_key)
    {
        $user = $this->Db_model->row('users', "activation_key = '{$activation_key}'");
        if ( ! is_null($user) ) {
            $this->Account_model->create_session($user->email, TRUE);
            //Asignar nuevo key, para deshabilitar actual
            $this->Account_model->activation_key($user->id);
            $this->logged();
        } else {
            redirect("app/accounts/login_link/{$activation_key}");
        }
    }
    
//REGISTRO DE USUARIOS
//---------------------------------------------------------------------------------------------------
    
    /**
     * FormULARIO de registro de nuevos usuarios en la aplicación
     * se envían los datos a admin/accounts/create
     */
    function signup()
    {
        $data['head_title'] = 'Crear tu cuenta de ' . APP_NAME ;
        $data['view_a'] = $this->views_folder . 'signup_v';
        //$data['g_client'] = $this->Account_model->g_client(); //Para botón login con Google
        $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
        $this->load->view('templates/easypml/start', $data);
    }

// ACTIVATION
//-----------------------------------------------------------------------------

    /**
     * Vista del resultado de activación de cuenta de usuario
     */
    function activation($activation_key = '')
    {
        $data['head_title'] = 'Activación de cuenta';
        $data['activation_key'] = $activation_key;
        $data['view_a'] = $this->views_folder . 'activation_v';

        $this->App_model->view('templates/easypml/start', $data);
    }

//RECUPERACIÓN DE CUENTAS
//---------------------------------------------------------------------------------------------------
    
    /**
     * Formulario para solicitar restaurar contraseña, se solicita email o nombre de usuario
     * Se genera user.activation_key, y se envía mensaje de correo eletrónico con link
     * para asignar nueva contraseña
     * 2020-07-20
     */
    function recovery()
    {
        if ( $this->session->userdata('logged') )
        {
            redirect('');
        } else {
            $data['head_title'] = 'Restaurar cuenta';
            $data['view_a'] = $this->views_folder . 'recovery_v';
            $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
            $this->load->view('templates/easypml/start', $data);
        }
    }

    /**
     * Formulario para reestablecer contraseña, se solicita nueva contraseña y 
     * confirmación
     * 2020-08-21
     */
    function recover($activation_key)
    {
        //Valores por defecto
            $data['head_title'] = 'Usuario no identificado';
            $data['user_id'] = 0;
        
        //Variables
            $row_user = $this->Db_model->row('users', "activation_key = '{$activation_key}'");        
            $data['activation_key'] = $activation_key;
            $data['row'] = $row_user;
        
        //Verificar que usuario haya sido identificado
            if ( ! is_null($row_user) ) 
            {
                $data['head_title'] = $row_user->display_name;
                $data['user_id'] = $row_user->id;
            }

        //Verificar que no tenga sesión iniciada
            if ( $this->session->userdata('logged') ) redirect('app/logged');

        //Cargar vista
            $data['view_a'] = $this->views_folder . 'recover_v';
            $this->load->view('templates/easypml/start', $data);
    }

// ADMINISTRACIÓN DE CUENTA
//-----------------------------------------------------------------------------

    /** Perfil del usuario en sesión */
    function profile()
    {        
        $data = $this->User_model->basic($this->session->userdata('user_id'));
        
        //Variables específicas
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'profile_v';
        
        $this->App_model->view('templates/easypml/main', $data);
    }

// ACTUALIZACIÓN DE DATOS
//-----------------------------------------------------------------------------

    /**
     * Formulario edición datos usuario en sessión. Los datos que se
     * editan dependen de la $section elegida.
     */
    function edit($section = 'basic')
    {
        //Datos básicos
        $user_id = $this->session->userdata('user_id');

        $data = $this->User_model->basic($user_id);

        $data['options_document_type'] = $this->Item_model->options('category_id = 53');
        $data['options_gender'] = $this->Item_model->options('category_id = 59 AND cod <= 2');
        $data['options_privacy'] = $this->Item_model->options('category_id = 66');
        $data['options_city_id'] = $this->App_model->options_place('type_id = 4 AND status = 1');
        
        $view_a = $this->views_folder . "edit/{$section}_v";
        if ( $section == 'cropping' )
        {
            $view_a = 'common/bs5/cropping_v';
            $data['image_id'] = $data['row']->image_id;
            $data['url_image'] = $data['row']->url_image;
            $data['back_destination'] = URL_APP . "accounts/edit/image";
        }
        
        //Array data espefícicas
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['nav_3'] = $this->views_folder . 'edit/menu_v';
            $data['view_a'] = $view_a;
        
        $this->App_model->view('templates/easypml/main', $data);
    }
}