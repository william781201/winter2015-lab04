<?php

/**
 * Order handler
 * 
 * Implement the different order handling usecases.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Order extends Application {

    function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder() {
        //FIXME
        $order_num = $this->Orders->highest() + 1;
        
        $neworder = $this->Orders->create();
        $neworder->num = $order_num;
        $neworder->date = date();
        $neworder->status = 'a';
        $neworder->total = 0;
        $this->Orders->add($neworder);

        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null) {
        if ($order_num == null)
            redirect('/order/neworder');

        $this->data['pagebody'] = 'show_menu';
        $this->data['order_num'] = $order_num;
        //FIXME
        $this->data['title'] = "Order # ".$order_num;

        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

	// Bit of a hokey patch here, to work around the problem of the template
	// parser no longer allowing access to a parent variable inside a
	// child loop - used for the columns in the menu display.
	// this feature, formerly in CI2.2, was removed in CI3 because
	// it presented a security vulnerability.
	// 
	// This means that we cannot reference order_num inside of any of the
	// variable pair loops in our view, but must instead make sure
	// that any such substitutions we wish make are injected into the 
	// variable parameters
	// Merge this fix into your origin/master for the lab!
	$this->hokeyfix($this->data['meals'],$order_num);
	$this->hokeyfix($this->data['drinks'],$order_num);
	$this->hokeyfix($this->data['sweets'],$order_num);
	// end of hokey patch
	
        $this->render();
    }

    // inject order # into nested variable pair parameters
    function hokeyfix($varpair,$order) {
	foreach($varpair as &$record)
	    $record->order_num = $order;
    }
    
    // make a menu ordering column
    function make_column($category) {
        //FIXME
        return $this->Menu->some('category', $category);
    }

    // add an item to an order
    function add($order_num, $item) {
        //FIXME
        $this->Orders->add_item($order_num, $item);
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num) {
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
        //FIXME
        $this->data['total'] = $this->orders->total($order_num);
        // Get the item list
        $items = $this->orderitems->group($order_num);
        
        foreach($items as $item)
        {
            $menuitem = $this->menu->get($item->item);
            $item->code = $menuitem->name;
        }
        
        $this->data['items'] = $items;
        $this->data['okornot'] = $this->orders->validate($order_num);
        $this->render();
    }

    // proceed with checkout
    function proceed($order_num) {
        //FIXME
        redirect('/');
    }

    // cancel the order
    function cancel($order_num) {
        //FIXME
        redirect('/');
    }

}
