<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $CI = &get_instance();
        if ($CI->Orderitems->exists($num, $code))
        {
            $record = $CI->Orderitems->get($num, $code);
            $record->quantity++;
            $CI->Orderitems->update($record);
        } else 
        {
            $record = $CI->Orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->Orderitems->add($record);
        }
    }

    // calculate the total for an order
    function total($num) {
        // the autoloaded orderitems is in the scope of the controller
        // we want our own access
        $CI = &get_instance();
        $CI->load->model('Orderitems');
        
        // get all the items in this order
        $items = $this->Orderitems->some('code', $num);
        
        // and add em up
        $result = 0;
        foreach ($items as $item)
        {
            $menuitem = $this->Menu->get($item->item);
            $result = $item->quantity * $menuitem->price;
        }
        
        return $result;
    }
    
    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        return false;
    }

}
