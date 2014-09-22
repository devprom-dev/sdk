<?php

 require_once('c_iterator.php');

 //////////////////////////////////////////////////////////////////////////////////////////////
 class UnionIterator extends OrderedIterator
 {
 	var $links;
 	var $current_link_number;
 	
 	function UnionIterator( $object ) {
 		parent::OrderedIterator( $object );
 		
 		$this->links = array();
 		$this->current_link_number = 0;
 	}
 	
 	function addLink( $iterator ) {
 		array_push($this->links, $iterator);
 	}
 	
 	function count() {
 		$count = 0;
 		for($i = 0; $i < count($this->links); $i++) {
 			$count += $this->links[$i]->count();
 		}
 		return $count;
 	}
 	
 	function moveFirst() {
 		$this->links[0]->moveFirst();
 		$this->useLink(0);
 	}
 	
 	function moveToPos( $offset ) {
 		$u_bound = 0;
 		for($i = 0; $i < count($this->links); $i++) {
 			$u_bound += $this->links[$i]->count();
 			if($u_bound > $offset) {
 				$this->links[$i]->moveToPos($offset - ($u_bound - $this->links[$i]->count()) );
 				$this->useLink($i);
 				break;
 			}
 		}
 	}
 	
 	function moveNext() {
		parent::moveNext();
		
 		if($this->pos >= $this->links[$this->current_link_number]->count()) {
 			if($this->current_link_number + 1 < count($this->links)) {
		 		$this->links[$this->current_link_number + 1]->moveFirst();
 				$this->useLink($this->current_link_number + 1);
 			}
 		}
 		else {
 			$this->links[$this->current_link_number]->data = $this->data;
 		}
 	}
 	
 	function useLink( $number ) {
 		$this->current_link_number = $number;
 		$this->rs = $this->links[$number]->rs;
 		$this->data = $this->links[$number]->data;
 		$this->object = $this->links[$number]->object;
 		$this->pos = $this->links[$number]->pos;
 		if($this->links[$number]->count() < 1) {
 			$this->moveNext();
 		}
 	}
 	
 	function getCurrentIt() {
 		return $this->links[$this->current_link_number];
 	}
 }
?>
