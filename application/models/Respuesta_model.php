<?php
class Respuesta_model extends CI_Model{

    function basic($post_id)
    {
        $row = $this->Db_model->row_id('posts', $post_id);

        $data['row'] = $row;
        $data['head_title'] = $data['row']->post_name;
        $data['view_a'] = $this->views_folder . 'post_v';
        $data['nav_2'] = $data['type_folder'] . 'menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - respuestas/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'respuestas';                       //Nombre del controlador
            $data['cf'] = 'respuestas/explore/';                      //Nombre del controlador
            $data['views_folder'] = 'admin/grafinar/respuestas/explore/';      //Carpeta donde están las vistas de exploración
            $data['numPage'] = $num_page;                       //Número de la página
            
        //Vistas
            $data['head_title'] = 'Respuestas';
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    function get($filters, $num_page, $per_page = 10)
    {
        //Load
            $this->load->model('Search_model');

        //Búsqueda y Resultados
            $data['filters'] = $filters;
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['list'] = $elements->result();
            $data['strFilters'] = $this->Search_model->str_filters($filters, TRUE);
            $data['qtyResults'] = $this->qty_results($filters);
            $data['maxPage'] = ceil($this->pml->if_zero($data['qtyResults'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de posts
     * 2022-08-23
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'posts.id, post_name AS title, posts.type_id, 
            slug, published_at, posts.status, content AS narracion,
            related_2 AS scene_id, 
            users.id AS user_id, users.display_name AS user_display_name';
        $arr_select['export'] = 'posts.id AS respuesta_id, post_name AS titulo,
            content AS narracion, content_json AS personajes_emociones,
            posts.related_2 AS escena_id, text_1 AS nombre_escena,
            posts.status AS estado_respuesta,
            users.id AS user_id, users.display_name AS user_display_name
            ';

        return $arr_select[$format];
    }
    
    /**
     * Query con resultados de posts filtrados, por página y offset
     * 2020-07-15
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Segmento SELECT
            $select_format = 'general';
            if ( $filters['sf'] != '' ) { $select_format = $filters['sf']; }
            $this->db->select($this->select($select_format));
            $this->db->join('users', 'posts.related_1 = users.id', 'left');
            
        
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('posts.updated_at', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('posts', $per_page, $offset); //Resultados por página
        
        return $query;
        
    }

    /**
     * String con condición WHERE SQL para filtrar post
     * 2022-05-02
     */
    function search_condition($filters)
    {
        $condition = 'posts.type_id = 129 AND ';

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $qWords = ['post_name', 'content', 'excerpt', 'keywords'];
        $words_condition = $this->Search_model->words_condition($filters['q'], $qWords);
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['status'] != '' ) { $condition .= "status = {$filters['status']} AND "; }
        if ( $filters['u'] != '' ) { $condition .= "related_1 = {$filters['u']} AND "; }
        if ( $filters['fe1'] != '' ) { $condition .= "related_2 = {$filters['fe1']} AND "; }
        if ( $filters['condition'] != '' ) { $condition .= "{$filters['condition']} AND "; }
        
        //Quitar cadena final de ' AND '
        if ( strlen($condition) > 0 ) { $condition = substr($condition, 0, -5);}
        
        return $condition;
    }
    
    /**
     * Devuelve la cantidad de registros encontrados en la tabla con los filtros
     * establecidos en la búsqueda
     */
    function qty_results($filters)
    {
        $this->db->select('id');
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('posts'); //Para calcular el total de resultados

        return $query->num_rows();
    }

    /**
     * Query para exportar
     * 2022-08-17
     */
    function query_export($filters)
    {
        //Select
        $select = $this->select('export');
        $this->db->join('users', 'posts.related_1 = users.id', 'left');

        if ( $filters['sf'] != '' ) { $select = $this->select($filters['sf']); }
        $this->db->select($select);

        //Condición Where
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}

        //Get
        $query = $this->db->get('posts', 10000);  //Hasta 10.000 registros

        return $query;
    }
    
    /**
     * Devuelve segmento SQL
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'posts.id > 0';  //Valor por defecto, ningún post, se obtendrían cero posts.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los post
            $condition = 'posts.id > 0';
        }
        
        return $condition;
    }
    
    /**
     * Array con options para ordenar el listado de post en la vista de
     * exploración
     */
    function order_options()
    {
        $order_options = array(
            '' => '[ Ordenar por ]',
            'id' => 'ID Post',
            'post_name' => 'Nombre'
        );
        
        return $order_options;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Objeto registro de un post ID, con un formato específico
     * 2021-01-04
     */
    function row($post_id, $format = 'general')
    {
        $row = NULL;    //Valor por defecto

        $this->db->select($this->select($format));
        $this->db->where('id', $post_id);
        $query = $this->db->get('posts', 1);

        if ( $query->num_rows() > 0 ) $row = $query->row();

        return $row;
    }

    /**
     * Guardar un registro en la tabla posts
     * 2022-07-27
     */
    function save($arr_row = null)
    {
        //Verificar si hay array con registro
        if ( is_null($arr_row) ) $arr_row = $this->Db_model->arr_row();

        //Verificar si tiene id definido, insertar o actualizar
        if ( ! isset($arr_row['id']) ) 
        {
            //No existe, insertar
            $arr_row['slug'] = $this->Db_model->unique_slug($arr_row['post_name'],'posts');
            $this->db->insert('posts', $arr_row);
            $post_id = $this->db->insert_id();
        } else {
            //Ya existe, editar
            $post_id = $arr_row['id'];
            unset($arr_row['id']);

            $this->db->where('id', $post_id)->update('posts', $arr_row);
        }

        $data['saved_id'] = $post_id;
        return $data;
    }

// ELIMINACIÓN DE UNA RESPUESTA
//-----------------------------------------------------------------------------
    
    /**
     * Verifica si el usuario en sesión tiene permiso para eliminar un registro
     * tabla post
     * 2020-08-18
     */
    function deleteable($row_id)
    {
        $row = $this->Db_model->row_id('posts', $row_id);

        $deleteable = 0;    //Valor por defecto

        //Es Administrador
        if ( in_array($this->session->userdata('role'), [1,2,3]) ) {
            $deleteable = 1;
        }

        //Es el creador
        if ( $row->creator_id = $this->session->userdata('user_id') ) {
            $deleteable = 1;
        }

        return $deleteable;
    }

    /**
     * Eliminar un post de la base de datos, se eliminan registros de tablas
     * relacionadas
     * 2022-08-20
     */
    function delete($post_id)
    {
        $qty_deleted = 0;

        if ( $this->deleteable($post_id) ) 
        {
            //Tablas relacionadas
                $this->db->where('parent_id', $post_id)->delete('posts');
            
            //Tabla principal
                $this->db->where('id', $post_id)->delete('posts');

            $qty_deleted = $this->db->affected_rows();  //De la última consulta, tabla principal

            //Eliminar archivos relacionados
            if ( $qty_deleted > 0 ) $this->delete_files($post_id);
        }

        return $qty_deleted;
    }

    /**
     * Eliminar los archivos relacionados con el post eliminado
     * 2021-02-20
     */
    function delete_files($post_id)
    {
        //Identificar archivos
        $this->db->select('id');
        $this->db->where("table_id = 2000 AND related_1 = {$post_id}");
        $files = $this->db->get('files');
        
        //Eliminar archivos
        $this->load->model('File_model');
        $session_data = $this->session->userdata();
        foreach ( $files->result() as $file ) {
            $this->File_model->delete($file->id, $session_data);
        }
    }

    /**
     * Query para exportar detalles de las respuestas
     * 2022-10-01
     */
    function query_export_details($filters)
    {
        $select = 'grf_answers.id AS detalle_id,
            user_id AS usuario_id, users.username,
            scene_id AS escena_id, answers.text_1 AS nombre_escena,
            answer_id AS respuesta_id,
            character_id AS personaje_id, characters.code AS codigo_personaje,
            characters.post_name AS personaje_nombre, genders.item_name AS personaje_sexo,
            age_groups.item_name AS personaje_grupo_edad,
            feeling_cod AS codigo_sentimiento, feelings.item_name AS sentimiento
        ';
        //Select
        $this->db->select($select);
        $this->db->join('users', 'grf_answers.user_id = users.id', 'left');
        $this->db->join('posts AS answers', 'grf_answers.answer_id = answers.id AND answers.type_id = 129', 'left');
        $this->db->join('posts AS characters', 'grf_answers.character_id = characters.id AND characters.type_id = 125', 'left');
        $this->db->join('items AS feelings', 'grf_answers.feeling_cod = feelings.cod AND feelings.category_id = 120', 'left');
        $this->db->join('items AS age_groups', 'characters.cat_1 = age_groups.cod AND age_groups.category_id = 101', 'left');
        $this->db->join('items AS genders', 'characters.cat_2 = genders.cod AND genders.category_id = 59', 'left');

        //Get
        $query = $this->db->get('grf_answers', 100000);  //Hasta 100.000 registros

        return $query;
    }
}