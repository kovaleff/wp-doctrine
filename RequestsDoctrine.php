<?php

require_once 'RequestsListTable.php';

class RequestsDoctrine{
    private $entityManager;

    function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function init(){
        $this->addShortcodes();
        $this->addActions();

    }

    function addShortcodes(){
        add_shortcode( 'request-form', function(){

            $users= $this->entityManager->getRepository('User')->findAll();
            $users = array_map(function ($user){
                return [
                    'id' => $user->ID,
                    'name' => $user->display_name
                ];
            }, $users);

            $form = "<form class='request-form p-2'>
        <div class='flex flex-column  align-center justify-center'>
        <label>Заголовок заявки:</label>
        <input name='request-title' class='m-1'>
        <label>Текст заявки:</label>
        <textarea name='request-content' class='m-1'></textarea>
        <select name='request-user'>";
            foreach($users as $user){
                $form .= "<option value='{$user['id']}'>{$user['name']}</option>";
            }
            $form .= "</select>
        <button type='submit' class='m-1'>Отправить</button>
        </div>
    </form>    
    ";
            echo $form;
        });
    }

    function addActions(){
        add_action('wp_enqueue_scripts', function () {
            global $post;
            if( $post && has_shortcode( $post?->post_content, 'request-form') ) {
                wp_enqueue_script( 'request-js', plugins_url( '/js/plugin-script.js', __FILE__ ), array('jquery'));
                wp_enqueue_style('', plugins_url( '/css/plugin-style.css', __FILE__ ));
            }
            wp_localize_script(
                'request-js',
                'request_obj',
                array(
                    'rest_url' => get_site_url( ) . '/wp-json/v1/form-request/user/',
                )
            );
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'v1/form-request/', '/user/(?P<id>\d+)', array(
                'methods' => 'POST',
                'callback' => [$this, 'hadleRestRequest'],
            ) );
        });

        add_action( 'admin_menu', function(){
            add_menu_page('Заявки', 'Список заявок',
                'manage_options', 'request-list', [$this, 'displayRequests'],
                'dashicons-smiley', 99);
        } );

    }

    function hadleRestRequest( WP_REST_Request $request){
        global $entityManager;
        $id = (int)$request->get_param('id');
        $title = sanitize_text_field($request->get_param('title'));
        $request = sanitize_text_field($request->get_param('request'));

        try {
            $user = $entityManager->find('User', $id);
            if ($user) {
                $requestEntity = new Request();
                $requestEntity->setTitle($title);
                $requestEntity->setDescription($request);
                $requestEntity->user = $user;
                $entityManager->persist($requestEntity);
                $entityManager->flush();
                $user->assignToRequests($requestEntity);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        } catch (\Doctrine\ORM\Exception\ORMException $e) {
            return new WP_REST_Response(array('result' => 'Ошибка подачи завки.'), 400);
        }
        return new WP_REST_Response(array('result' => 'Создана заявка №:'.$requestEntity->getId()), 200);
    }


    function displayRequests(){
        global $entityManager;

        echo '<div class="wrap"><h2>Заявки</h2>';
        $table = new RequestsListTable($entityManager);
        $table->prepare_items();
        $table->display();
        echo '</div>';
    }


}

