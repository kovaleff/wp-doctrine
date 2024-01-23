<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class RequestsListTable extends WP_List_Table
{
    public $entityManager;

    function __construct($entityManager){
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    function prepare_items() {
        $pageSize = 5;
        $page = $this->get_pagenum();
        $requestArr = [];

        $requests= $this->entityManager->getRepository('Request');
        $query = $requests->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC')
            ->getQuery();

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);
        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        foreach ($paginator as $request) {
            $requestArr[] = [
                'id' => $request->getId(),
                'title' => $request->getTitle(),
                'request' => $request->getDescription(),
            ];
        }

        $this->set_pagination_args(array(
            'total_items' => $totalItems, // total number of items
            'per_page'    => $pageSize, // items to show on a page
            'total_pages' => ceil( $totalItems / $pageSize ) // use ceil to round up
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary  = 'id';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        $this->items = $requestArr;
    }

    function get_columns(){
        $columns = array(
            'id'            => 'id',
            'title'          => 'Заголовок',
            'request'         => 'Заявка',
        );
        return $columns;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'title':
            case 'request':
            default:
                return $item[$column_name];
        }
    }
}

