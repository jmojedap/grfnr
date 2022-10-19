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
     * 2022-10-09
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'posts.id, post_name AS title, posts.type_id, 
            slug, published_at, posts.status, content AS narracion,
            related_2 AS scene_id, 
            users.id AS user_id, users.display_name AS user_display_name';
        $arr_select['export'] = 'posts.id AS respuesta_id, posts.post_name AS titulo,
            posts.content AS narracion, posts.content_json AS personajes_emociones,
            posts.related_2 AS escena_id, posts.text_1 AS nombre_escena,
            posts.status AS estado_respuesta, posts.updated_at AS fecha_guardado,
            users.id AS user_id, users.display_name AS usuario,
            users.school_level AS grado_escolar, 
            posts.text_2 AS genero_usuario, posts.integer_1 AS edad_usuario,
            institutions.post_name AS institucion,
            places.full_name AS ciudad
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
        if ( $filters['org'] != '' ) { $condition .= "parent_id = {$filters['org']} AND "; }
        if ( $filters['level'] != '' ) { $condition .= "integer_2 = {$filters['level']} AND "; }
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
        $this->db->join('posts AS institutions', 'users.organization_id = institutions.id', 'left');
        $this->db->join('places', 'institutions.place_id = places.id', 'left');

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

// PROCESO DE RESPUESTA
//-----------------------------------------------------------------------------

    /**
     * Crea el registro en la tabla posts, type_id 129, respuesta
     * a una escena, con los valores de emociones en blanco
     * 2022-10-18
     * 
     */
    function iniciar($escena_id, $user_id)
    {
        // Resultado por defecto
        $data = array('status' => 0, 'saved_id' => 0);

        $escena = $this->Escena_model->row($escena_id);
        $user = $this->Db_model->row_id('users', $user_id);

        // Si la escena y el usuario existen:
        if ( ! is_null($escena) && ! is_null($user) ) {
            $arr_row = $this->Db_model->arr_row(false); //Datos base

            $arr_row['type_id'] = 129;
            $arr_row['post_name'] = 'Respuesta a: ' . $escena->title;
            $arr_row['content_json'] = $this->Escena_model->personajes_json($escena_id);
            $arr_row['status'] = 5; //Iniciada
            $arr_row['related_1'] = $user->id;
            $arr_row['related_2'] = $escena->id;
            $arr_row['parent_id'] = $user->organization_id; //Institución
            $arr_row['integer_1'] = $this->pml->age($user->birth_date);
            $arr_row['integer_2'] = $user->organization_id; //Nivel escolar

            //Condición, un usuario solo puede responder una escena una vez
            $condition = "type_id = {$arr_row['type_id']}
                AND related_1 = {$arr_row['related_1']}
                AND related_2 = {$arr_row['related_2']}";

            $data['saved_id'] = $this->Db_model->insert_if('posts', $condition, $arr_row);
            if ( $data['saved_id'] > 0 ) { $data['status'] = 1; }
        }
    
        return $data;
    }

    /**
     * Actualiza registro de respuesta type_id 129, tabla posts
     * 2022-09-03
     */
    function guardar($escena_id, $respuesta_id)
    {
        $arr_row['status'] = 2; //En proceso
        $arr_row['content'] = $this->input->post('content');
        $arr_row['content_json'] = $this->input->post('content_json');
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        $condition = "id = {$respuesta_id} AND related_2 = {$escena_id}";
        $data['saved_id'] = $this->Db_model->save('posts', $condition, $arr_row);
        $data['respuesta_status'] = $arr_row['status'];
    
        return $data;
    }

    /**
     * Marcar un posts respuesta como finalizado
     * 2022-09-04
     */
    function finalizar($escena_id, $respuesta_id)
    {
        $data = array('saved_id' => 0, 'message' => 'No se pudo finalizar la respuesta');

        //Condición de identificación, estado y datos
        $condition = "type_id = 129 AND id = {$respuesta_id}
            AND related_2 = {$escena_id} AND status = 2";
        $respuesta = $this->Db_model->row('posts', $condition);

        if ( ! is_null($respuesta) ) {
            $arr_row['status'] = 1; //Finalizada
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['updated_at'] = date('Y-m-d H:i:s');

            $condition = "id = {$respuesta->id}";
            $data['saved_id'] = $this->Db_model->save('posts', $condition, $arr_row);
            if ( $data['saved_id'] > 0 ) {
                $data['message'] = 'Respuesta finalizada';
            }

            $data['details'] = $this->guardar_detalle($escena_id, $respuesta_id);
        }

        return $data;
    }

    /**
     * Guardar detalle de emociones de una respuesta en la tabla gfr_answers
     * 2022-09-05
     */
    function guardar_detalle($escena_id, $respuesta_id)
    {
        $data = array('status' => 0, 'qty_details' => 0);

        $escena = $this->Escena_model->row($escena_id);
        $respuesta = $this->Db_model->row_id('posts', $respuesta_id);

        $emocionesPersonajes = json_decode($respuesta->content_json);
        
        //Preventivo, eliminar detalle ya existente
        $sql = "DELETE FROM grf_answers WHERE scene_id = {$escena_id} AND answer_id = {$respuesta_id}";
        $this->db->query($sql);

        $arr_row['user_id'] = $respuesta->related_1;
        $arr_row['scene_id'] = $escena_id;
        $arr_row['answer_id'] = $respuesta_id;

        foreach ($emocionesPersonajes as $emocionPersonaje) {
            $arr_row['character_id'] = $emocionPersonaje->character_id;
            $arr_row['feeling_cod'] = $emocionPersonaje->feeling_cod;

            $this->db->insert('grf_answers', $arr_row);

            if ( $this->db->insert_id() ) { $data['qty_details']++; }
        }

        //Extra control, eliminar detalles huérfanos
        $this->limpiar_detalle();

        return $data;
    }

    /**
     * Eliminar registros de la tabla grf_answers de respuestas
     * que ya no existan en la tabla posts.
     * 2022-10-18
     */
    function limpiar_detalle()
    {
        $sql = 'DELETE FROM grf_answers
            WHERE answer_id NOT IN (SELECT id FROM posts)';
        $this->db->query($sql);
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
    function delete($answer_id)
    {
        $qty_deleted = 0;

        if ( $this->deleteable($answer_id) ) 
        {
            //Tablas relacionadas
                $this->db->where('answer_id', $answer_id)->delete('grf_answers');
                $this->db->where('answer_id', $answer_id)->delete('grf_words');
            
            //Tabla principal
                $this->db->where('id', $answer_id)->delete('posts');

            $qty_deleted = $this->db->affected_rows();  //De la última consulta, tabla principal
        }

        return $qty_deleted;
    }

    /**
     * Query para exportar detalles de las respuestas
     * 2022-10-01
     */
    function query_export_details($filters)
    {
        $select = 'answer_id AS respuesta_id, grf_answers.id AS detalle_id,
            user_id AS usuario_id, users.username,
            scene_id AS escena_id, answers.text_1 AS nombre_escena,
            character_id AS personaje_id, characters.code AS codigo_personaje,
            characters.post_name AS personaje_nombre, genders.item_name AS personaje_sexo,
            age_groups.item_name AS personaje_grupo_edad,
            feeling_cod AS codigo_emocion, feelings.item_name AS emocion
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

// Procesamiento y cálculos
//-----------------------------------------------------------------------------

    /**
     * Actualizar tabla gfr_words, con las palabras de las narraciones de las
     * respuestas
     */
    function update_answer_words()
    {
        $no_words_str = file_get_contents(PATH_RESOURCES . "config/grafinar/no_words.json");
        $no_words = json_decode($no_words_str);

        $special_characters = ['.',',',';','"','-','',':','–','”','“'];
        $texts = [];
        $qty_words = 0;

        $answers = $this->answers_unprocessed();
        foreach($answers->result() as $answer ) {
            $text = trim($answer->content);
            $text = mb_strtolower($text,'UTF-8');
            $text = str_replace($special_characters,'',$text);  //Sin puntuación
            $text = str_replace('   ',' ',$text);    //Sin triple espacio
            $text = str_replace('  ',' ',$text);    //Sin doble espacio
            $arr_words = explode(' ',$text);
            $arr_words = array_diff($arr_words,$no_words);
            //$texts[] = $text;

            $qty_words += $this->save_answer_words($answer, $arr_words);
        }

        $data['status'] = 1;
        $data['message'] = "Se agregaron {$qty_words} palabras de {$answers->num_rows()} narraciones.";

        return $data;
    }

    function answers_unprocessed()
    {
        $this->db->select('id,content, related_2 AS scene_id, related_1 AS user_id');
        $this->db->where('type_id', 129);   //Post respuesta
        $this->db->where('status', 1);      //Finalizadas
        $this->db->where('content <>', '');      //Con contenido
        $this->db->where('id NOT IN (SELECT answer_id FROM grf_words)');    //Sin procesar palabras
        $answers = $this->db->get('posts');

        return $answers;
    }

    /**
     * Guardar en la tabla grf_words, las palabras de una narrración específica
     * 2022-10-15
     */
    function save_answer_words($answer, $arr_words)
    {
        $this->db->delete('grf_words', "answer_id = {$answer->id}");

        $arr_row['scene_id'] = $answer->scene_id;
        $arr_row['answer_id'] = $answer->id;
        $arr_row['user_id'] = $answer->user_id;

        $words_ids = [];

        foreach ($arr_words as $word_index => $word) {
            $arr_row['word_index'] = $word_index;
            $arr_row['word'] = $word;
            $condition = "word = '{$arr_row['word']}' AND scene_id = {$arr_row['scene_id']} AND 
                answer_id = {$arr_row['answer_id']} AND user_id = {$arr_row['user_id']}";

            $word_id = $this->Db_model->exists('grf_words', $condition);
            if ( $word_id == 0 ) {
                $words_ids[] = $this->Db_model->save('grf_words', $condition, $arr_row);
            } else {
                $sql = "UPDATE grf_words SET quantity = quantity + 1 WHERE id = {$word_id}";
                $this->db->query($sql);
                if (  $this->db->affected_rows() > 0 ) {
                    $words_ids[] = $word_id;
                }
            }
        }

        return count($words_ids);
    }

    /**
     * Query para exportar
     * 2022-08-17
     */
    function words_query_export($filters)
    {
        //Select
        $select = 'grf_words.id AS palabra_id, word AS palabra, quantity AS cantidad_usada,
             username, school_level AS grado_escolar, answers.integer_1 AS edad_usuario,
             answers.text_2 AS genero usuario,
             scene_id AS escena_id, scenes.post_name AS escena';
        $this->db->join('users', 'grf_words.user_id = users.id', 'left');
        $this->db->join('posts AS scenes', 'grf_words.scene_id = scenes.id', 'left');
        $this->db->join('posts AS answers', 'grf_words.answer_id = answers.id', 'left');
        
        $this->db->select($select);
        //Get
        $query = $this->db->get('grf_words', 100000);  //Hasta 100.000 registros

        return $query;
    }


}