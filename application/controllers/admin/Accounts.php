<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/accounts/';
    public $url_controller = URL_ADMIN . 'accounts/';

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
                $this->load->view('templates/admin_pml/start', $data);
            }
    }

    /**
     * Recibe datos POST de accounts/login
     */
    function validate_login()
    {
        //Setting variables
            $userlogin = $this->input->post('username');
            $password = $this->input->post('password');
            
            $data = $this->Account_model->validate_login($userlogin, $password);
            
            if ( $data['status'] )
            {
                $this->Account_model->create_session($userlogin, TRUE);
            }
            
        //Salida
            $this->output->set_content_type('application/json')->set_output(json_encode($data));      
    }
    
    /**
     * Destroy session and redirect to login, start.
     * 2022-03-08
     */
    function logout($type = '')
    {
        $this->Account_model->logout();
        if ( $type == 'ajax' ) {
            $data['status'] = 1;
            $this->output->set_content_type('application/json')->set_output(json_encode($data));      
        } else {
            redirect('app/accounts/login');
        }
    }

    /**
     * ml > master login
     * Función para el login de administradores ingresando con otro user
     * 
     * @param type $user_id
     */
    function ml($user_id)
    {
        $username = $this->Db_model->field_id('users', $user_id, 'username');
        if ( in_array($this->session->userdata('role'), array(1,2)) ) {
            $this->Account_model->create_session($username, FALSE);
        }
        
        redirect('app/accounts/logged');
    }

// MAGIC LINK
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Enviar por correo electrónico un link para iniciar sesión en la
     * aplicación
     * 2022-08-06
     */
    function get_login_link()
    {
        $email = $this->input->post('email');

        //Respuesta por defecto
        $data = [
            'status' => 0,
            'message' => "No existe ningún usuario con el correo '{$email}'",
            'link' => ''
        ];

        //Identificar usuario
        $email = $this->input->post('email');
        $user = $this->Db_model->row('users', "email = '{$email}'");
        
        if ( ! is_null($user) ) {
            if ( ENV == 'production') {
                $this->load->model('Notification_model');
                $data['status'] = $this->Notification_model->send_login_link($user->id);
                $data['message'] = "El link fue enviado a el correo electrónico {$email}";
            } else {
                $data['status'] = 1;
                $data['link'] =  URL_APP . "accounts/validate_login_link/{$activation_key}";
                $data['message'] = 'Mensaje no enviado - Versión local';
            }
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
        
    }
    
//REGISTRO DE USUARIOS
//---------------------------------------------------------------------------------------------------

    /**
     * AJAX JSON
     * 
     * Recibe los datos POST del form en accounts/signup. Si se validan los 
     * datos, se registra el user. Se devuelve $data, con resultados de registro
     * o de validación (si falló).
     * 2022-07-29
     */
    function create()
    {
        //Validar Recaptcha
        $this->load->model('Validation_model');
        $recaptcha = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3

        //Validar Formulario
        $res_validation = $this->Account_model->validate_form();

        //Resultado inicial por defecto
        $data = [
            'saved_id' => 0,
            'recaptcha' => $recaptcha,
            'validation' => $res_validation['validation']
        ];
        
        //Comprobar 2 validaciones
        if ( $res_validation['status'] && $recaptcha == 1 )
        {
            //Construir registro del nuevo user
                $arr_row['display_name'] = $this->input->post('display_name');
                $arr_row['email'] = $this->input->post('email');
                $arr_row['username'] = $this->User_model->email_to_username($this->input->post('email'));
                $arr_row['password'] = $this->Account_model->crypt_pw($this->input->post('new_password'));

            //Crerar usuario, tabla users
                $data['saved_id'] = $this->User_model->save($arr_row);
                if ( $data['saved_id'] > 0 ) {
                    //Enviar email con código de activación
                    $this->Account_model->activation_key($data['saved_id']);
                    if ( ENV == 'production' ) {
                        $this->Notification_model->email_activation($data['saved_id']);
                    }
        
                    //Iniciar sesión
                    $this->Account_model->create_session($arr_row['email']);
                }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Validación de datos de accounts/signup
     * 2021-03-09
     */
    function validate_signup()
    {
        $data = $this->Account_model->validate_form();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Verificar si un email ya está registrado para una cuenta de usuario
     */
    function check_email()
    {
        $data = array('status' => 0, 'user' => array());

        $row = $this->Db_model->row('users', "email = '{$this->input->post('email')}'");

        if ( ! is_null($row))
        {
            $data['status'] = 1;
            $data['user']['firts_name'] = $row->first_name;
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// NOTIFICATIONS
//-----------------------------------------------------------------------------

    /**
     * Vista previa de mensajes de email, notificaciones
     * 2021-07-27
     */
    function test_notification_message($type = 'activation', $param_1 = 0, $param_2 = 0)
    {
        $this->load->model('Notification_model');
        if ( $type == 'activation' ) {
            $this->Account_model->activation_key($param_1);
            $user = $this->Db_model->row_id('users', $param_1);
            echo $this->Notification_model->activation_message($user, 'activation');
        } elseif ( $type == 'recovery' ) {
            $this->Account_model->activation_key($param_1);
            $user = $this->Db_model->row_id('users', $param_1);
            echo $this->Notification_model->activation_message($user, 'recovery');
        } elseif ( $type == 'login_link' ) {
            $this->Account_model->activation_key($param_1);
            $user = $this->Db_model->row_id('users', $param_1);
            echo $this->Notification_model->login_link_message($user, 'recovery');
        }
    }

// ACTIVATION
//-----------------------------------------------------------------------------

    /**
     * Ejecuta la activación de una cuenta de usuario ($activation_key)
     * 2022-08-08
     */
    function activate($activation_key = '')
    {
        $data = array('status' => 0, 'user_id' => 0, 'display_name' => '');
        $user = $this->Account_model->activate($activation_key);
        
        if ( ! is_null($user) )
        {
            $data['status'] = 1;
            $data['user_id'] = $user->id;
            $data['display_name'] = $user->display_name;

            //Establecer nueva activation_key
            $this->Account_model->activation_key($user->id);

            //Iniciar sesión
            $this->Account_model->create_session($user->email);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

//RECUPERACIÓN DE CUENTAS
//---------------------------------------------------------------------------------------------------

    /**
     * Recibe email por post desde app/accounts/recovery, y si encuentra 
     * usuario, envía link para establecer nueva contraseña
     * 2022-07-29
     */
    function recovery_email()
    {
        $data = ['status' => 0, 'recaptcha_valid' => FALSE];

        $this->load->model('Validation_model');
        $recaptcha = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3

        //Identificar usuario
        $row = $this->Db_model->row('users', "email = '{$this->input->post('email')}'");

        if ( ! is_null($row) && $recaptcha == 1 ) 
        {
            //Usuario existe, se envía email para restaurar constraseña
            $this->Account_model->activation_key($row->id);
            if ( ENV == 'production') {
                $this->load->model('Notification_model');
                $this->Notification_model->email_activation($row->id, 'recovery');
            }
            $data = ['status' => 1, 'recaptcha_valid' => TRUE];
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Recibe datos de POST y establece nueva contraseña a un usuario asociado a la $activation_key
     * 2020-07-20
     */
    function reset_password($activation_key)
    {
        $data = array('status' => 0, 'errors' => '');
        $row_user = $this->Db_model->row('users', "activation_key = '{$activation_key}'");        
        
        //Validar condiciones
        if ( $this->input->post('password') <> $this->input->post('passconf') ) $data['errors'] .= 'Las contraseñas no coinciden. ';
        if ( is_null($row_user) ) $data['errors'] .= 'Usuario no identificado. ';
        
        if ( strlen($data['errors']) == 0 ) 
        {
            $this->Account_model->change_password($row_user->id, $this->input->post('password'));
            $this->Account_model->create_session($row_user->username, 1);
            
            $data['status'] = 1;
            $data['message'] = $this->input->post('password') . '::' . $this->input->post('conf');
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// ACTUALIZACIÓN DE DATOS
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Se validan los datos del usuario en sesión, los datos deben cumplir varios criterios
     */
    function validate_form()
    {
        $user_id = $this->session->userdata('user_id');

        $data = $this->Account_model->validate_form($user_id);
        
        //Enviar result
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * POST JSON
     * Actualiza los datos del usuario en sesión.
     * 2022-07-29
     */
    function update()
    {
        $user_id = $this->session->userdata('user_id');
        $data['validation_data'] = $this->Account_model->validate_form($user_id);
        if ( $data['validation_data']['status'] == 1 ) {
            $arr_row = $this->input->post();
            $arr_row['id'] = $user_id;
            $data['saved_id'] = $this->Db_model->save_id('users', $arr_row);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Ejecuta el proceso de cambio de contraseña de un usuario en sesión
     * 2021-03-11
     */
    function change_password()
    {
        //Valores iniciales para el resultado del proceso
            $row_user = $this->Db_model->row_id('users', $this->session->userdata('user_id'));
            $validation = array('current_password' => 0, 'passwords_match' => 0);
            $data = array('status' => 0, 'errors' => array(), 'validation' => $validation);
        
        //Regla 1: Verificar contraseña actual
            $validar_pw = $this->Account_model->validate_password($row_user->username, $this->input->post('current_password'));
            if ( $validar_pw['status'] == 1 ) {
                $data['validation']['current_password'] = 1;
            } else {
                $data['errors'][] = 'La contraseña actual es incorrecta';
            }
        
        //Regla 2: Verificar que contraseña nueva coincida con la confirmación
            if ( $this->input->post('password') == $this->input->post('passconf') ) {
                $data['validation']['passwords_match'] = 1;
            } else {
                $data['errors'][] = 'La contraseña de confirmación no coincide.';
            }
        
        //Verificar condiciones necesarias
            if ( count($data['errors']) == 0 )
            {
                $this->Account_model->change_password($row_user->id, $this->input->post('password'));
                $data['status'] = 1;
                $data['message'] = 'Contraseña modificada';
            }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));   
    }

//IMAGEN DE PERFIL
//---------------------------------------------------------------------------------------------------

    /**
     * Carga archivo de imagen, y se la asigna como imagen de perfil al usuario en sesión
     * 2021-02-19
     */
    function set_image()
    {
        $user_id = $this->session->userdata('user_id');

        //Cargue
        $this->load->model('File_model');
        
        $data_upload = $this->File_model->upload($this->session->userdata('user_id'));
        
        $data = array('status' => 0, 'message' => 'La imagen no fue asignada');
        if ( $data_upload['status'] )
        {
            $this->User_model->remove_image($user_id);                              //Quitar image actual, si tiene una
            $data = $this->User_model->set_image($user_id, $data_upload['row']->id);   //Asignar imagen nueva
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX
     * Desasigna y elimina la imagen asociada (si la tiene) al usuario en sesión.
     */
    function remove_image()
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->User_model->remove_image($user_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}