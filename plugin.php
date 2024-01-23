<?php
/**
 * @package Hello_Dolly
 * @version 1.7.2
 */
/*
Plugin Name: Заявки Doctrine
Author: kovaleff
*/
$entityManager = require_once __DIR__."/doctrine/bootstrap.php";
require_once 'RequestsDoctrine.php';

$mainClass = new RequestsDoctrine($entityManager);
$mainClass->init();

register_activation_hook( __FILE__, function() use($entityManager) {
    global $wpdb;

    if($wpdb->get_var("show tables like 'requests'") == NULL){

        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = array(
            $entityManager->getClassMetadata('Request'),
        );
        try {
            $tool->createSchema($classes);
        }catch (PDOException $e){
//            ...
        }
    }
});


//add_shortcode( 'request-form', function() use($entityManager){
//
//    $users= $entityManager->getRepository('User')->findAll();
//    $users = array_map(function ($user){
//        return [
//            'id' => $user->ID,
//            'name' => $user->display_name
//        ];
//    }, $users);
//
//    $form = "<form class='request-form p-2'>
//        <div class='flex flex-column  align-center justify-center'>
//        <label>Заголовок заявки:</label>
//        <input name='request-title'>
//        <label>Текст заявки:</label>
//        <textarea name='request-content'></textarea>
//        <select name='request-user'>";
//        foreach($users as $user){
//            $form .= "<option value='{$user['id']}'>{$user['name']}</option>";
//        }
//        $form .= "</select>
//        <button type='submit'>Отправить</button>
//        </div>
//    </form>
//    ";
//    echo $form;
//});

//add_action('wp_enqueue_scripts', function () {
//    global $post;
//    if( $post && has_shortcode( $post?->post_content, 'request-form') ) {
//        wp_enqueue_script( 'request-js', plugins_url( '/js/plugin-script.js', __FILE__ ), array('jquery'));
//        wp_enqueue_style('', plugins_url( '/css/plugin-style.css', __FILE__ ));
//    }
//    wp_localize_script(
//        'request-js',
//        'request_obj',
//        array(
//            'rest_url' => get_site_url( ) . '/wp-json/v1/form-request/user/',
//        )
//    );
//});

//add_action( 'rest_api_init', function () {
//    register_rest_route( 'v1/form-request/', '/user/(?P<id>\d+)', array(
//        'methods' => 'POST',
//        'callback' => 'hadle_request',
//    ) );
//});

//function hadle_request( WP_REST_Request $request){
//    global $entityManager;
//    $id = (int)$request->get_param('id');
//    $title = sanitize_text_field($request->get_param('title'));
//    $request = sanitize_text_field($request->get_param('request'));
//
//    try {
//        $user = $entityManager->find('User', $id);
//        if ($user) {
//            $requestEntity = new Request();
//            $requestEntity->setTitle($title);
//            $requestEntity->setDescription($request);
//            $requestEntity->user = $user;
//            $entityManager->persist($requestEntity);
//            $entityManager->flush();
//            $user->assignToRequests($requestEntity);
//            $entityManager->persist($user);
//            $entityManager->flush();
//        }
//    } catch (\Doctrine\ORM\Exception\ORMException $e) {
//        return new WP_REST_Response(array('result' => 'Ошибка подачи завки.'), 400);
//    }
//    return new WP_REST_Response(array('result' => 'Создана заявка №:'.$requestEntity->getId()), 200);
//
//}

//add_action( 'admin_menu', function(){
//    add_menu_page('Заявки', 'Список заявок',
//        'manage_options', 'request-list', 'display_requests',
//        'dashicons-smiley', 99);
//} );

//function display_requests(){
//    global $entityManager;
//    if (!class_exists('WP_List_Table')) {
//        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
//    }
//
//    class Requests_List_Table extends WP_List_Table
//    {
//        public $entityManager;
//
//        function __construct($entityManager){
//            $this->entityManager = $entityManager;
//            parent::__construct();
//        }
//
//        function prepare_items() {
//            $pageSize = 5;
//            $page = $this->get_pagenum();
//            $requestArr = [];
//
//            $requests= $this->entityManager->getRepository('Request');
//            $query = $requests->createQueryBuilder('r')
//                ->orderBy('r.id', 'DESC')
//                ->getQuery();
//
//            $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
//            $totalItems = count($paginator);
//            $pagesCount = ceil($totalItems / $pageSize);
//            $paginator
//                ->getQuery()
//                ->setFirstResult($pageSize * ($page-1)) // set the offset
//                ->setMaxResults($pageSize); // set the limit
//
//            foreach ($paginator as $request) {
//                $requestArr[] = [
//                    'id' => $request->getId(),
//                    'title' => $request->getTitle(),
//                    'request' => $request->getDescription(),
//                ];
//            }
//
//            $this->set_pagination_args(array(
//                'total_items' => $totalItems, // total number of items
//                'per_page'    => $pageSize, // items to show on a page
//                'total_pages' => ceil( $totalItems / $pageSize ) // use ceil to round up
//            ));
//
//            $columns = $this->get_columns();
//            $hidden = array();
//            $sortable = array();
//            $primary  = 'id';
//            $this->_column_headers = array($columns, $hidden, $sortable, $primary);
//            $this->items = $requestArr;
//        }
//
//        function get_columns(){
//            $columns = array(
//                'id'            => 'id',
//                'title'          => 'Заголовок',
//                'request'         => 'Заявка',
//            );
//            return $columns;
//        }
//
//        function column_default($item, $column_name)
//        {
////            var_dump('!');exit;
//            switch ($column_name) {
//                case 'id':
//                case 'title':
//                case 'request':
//                default:
//                    return $item[$column_name];
//            }
//        }
//    }
//
//    echo '<div class="wrap"><h2>Заявки</h2>';
//    $table = new Requests_List_Table($entityManager);
//    $table->prepare_items();
//    $table->display();
//    echo '</div>';
//}

