<?php 
/**
 * Class Pagination
 */
class PAFB_Pagination {

		/*Default values*/
		var $total_pages = -1;//items
		var $limit = NULL;
		var $target = ""; 
		var $uriExtend = "";
		var $page = 1;
		var $adjacents = 2;
		var $showCounter = false;
		var $className = "pafb-pagination";
		var $parameterName = "page";
		var $urlF = false;//urlFriendly
		/*Buttons next and previous*/
		var $nextT = "Next";
		var $nextI = "&#187;"; //&#9658;
		var $prevT = "Previous";
		var $prevI = "&#171;"; //&#9668;
		/*****/
		var $calculate = false;
		
		#Total items
		function items($value){$this->total_pages = (int) $value;}
		
		#how many items to show per page
		function limit($value){$this->limit = (int) $value;}
		
		#Page to sent the page value
		function target($value, $uriExtend = ""){$this->target = $value; $this->uriExtend = $uriExtend;}
		
		#Current page
		function currentPage($value){$this->page = (int) $value;}
		
		#How many adjacent pages should be shown on each side of the current page?
		function adjacents($value){$this->adjacents = (int) $value;}
		
		#show counter?
		function showCounter($value=""){$this->showCounter=($value===true)?true:false;}
		#to change the class name of the pagination div
		function changeClass($value=""){$this->className=$value;}
		function nextLabel($value){$this->nextT = $value;}
		function nextIcon($value){$this->nextI = $value;}
		function prevLabel($value){$this->prevT = $value;}
		function prevIcon($value){$this->prevI = $value;}
		#to change the class name of the pagination div
		function parameterName($value=""){$this->parameterName=$value;}
		#to change urlFriendly
		function urlFriendly($value="%"){
			if(eregi('^ *$',$value)){
					$this->urlF=false;
					return false;
				}
			$this->urlF=$value;
		}
		
		var $pagination;
		function pagination(){}
		function show(){
			if(!$this->calculate)
				if($this->calculate())
					return "<div class=\"$this->className\">$this->pagination</div>\n";
		}

		function getOutput(){
			if(!$this->calculate)
				if($this->calculate())
					return "<div class=\"$this->className\">$this->pagination</div>\n";
		}

		function get_pagenum_link($id) {
			if (substr($this->target, 0, 9) == "admin.php" || substr($this->target, 0, 8) == "edit.php") {
				// We are in Admin
			
				if (strpos($this->target,'?') === false) {
					if ($this->urlF) {
						return str_replace($this->urlF,$id,$this->target);
					} else {
						return "$this->target?$this->parameterName=$id";
					}
				} else {
						return "$this->target&$this->parameterName=$id";
				}
			
			} else {
				
				// We are in Page		
				preg_match('/[0-9]/', $this->target, $matches, PREG_OFFSET_CAPTURE);
				if (isset($matches[0][1]) && $matches[0][1] > 0) {
					return substr($this->target, 0, $matches[0][1]) ."/$id/?$this->uriExtend";
				} else {
					return "$this->target/$id/?$this->uriExtend";
				}
				
			} // End Admin/Page Toggle
		}
		
		function calculate(){
				$this->pagination = "";
				$this->calculate == true;
				$error = false;
				if($this->urlF and $this->urlF != '%' and strpos($this->target,$this->urlF)===false){
						//Es necesario especificar el comodin para sustituir
						echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
						$error = true;
					}elseif($this->urlF and $this->urlF == '%' and strpos($this->target,$this->urlF)===false){
						echo "Es necesario especificar en el target el comodin % para sustituir el número de página<br />";
						$error = true;
					}
				if($this->total_pages < 0){
						echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
						$error = true;
					}
				if($this->limit == NULL){
						echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
						$error = true;
					}
				if($error)return false;
				
				$n = trim('<span>'. $this->nextT.'</span> '.$this->nextI);
				$p = trim($this->prevI.' <span>'.$this->prevT .'</span>');
				
				/* Setup vars for query. */
				if($this->page) 
					$start = ($this->page - 1) * $this->limit;      //first item to display on this page
				else
					$start = 0;                               		//if no page var is given, set start to 0
			
				/* Setup page vars for display. */
				$prev = $this->page - 1;                            //previous page is page - 1
				$next = $this->page + 1;                            //next page is page + 1
				$lastpage = ceil($this->total_pages/$this->limit);  //lastpage is = total pages / items per page, rounded up.
				$lpm1 = $lastpage - 1;                        		//last page minus 1
				
				/* 
					Now we apply our rules and draw the pagination object. 
					We're actually saving the code to a variable in case we want to draw it more than once.
				*/
				
				if($lastpage > 1){
					if($this->page){
						//anterior button
						if($this->page > 1)
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\" class=\"pagedir prev\">$p</a>";
							else
								$this->pagination .= "<span class=\"pagedir disabled\">$p</span>";
					}
					//pages	
					if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
						for ($counter = 1; $counter <= $lastpage; $counter++){
								if ($counter == $this->page)
										$this->pagination .= "<span class=\"pageno current\">$counter</span>";
									else
										$this->pagination .= "<a class=\"pageno\" href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
							}
					}
					elseif($lastpage > 5 + ($this->adjacents * 2)){//enough pages to hide some
						//close to beginning; only hide later pages
						if($this->page < 1 + ($this->adjacents * 2)){
								for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
										if ($counter == $this->page)
												$this->pagination .= "<span class=\"current\">$counter</span>";
											else
												$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
									}
								$this->pagination .= "...";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
							}
						//in middle; hide some front and some back
						elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)){
								$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
								$this->pagination .= "...";
								for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
									if ($counter == $this->page)
											$this->pagination .= "<span class=\"current\">$counter</span>";
										else
											$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								$this->pagination .= "...";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
							}
						//close to end; only hide early pages
						else {
								$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
								$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
								$this->pagination .= "...";
								for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
									if ($counter == $this->page)
											$this->pagination .= "<span class=\"current\">$counter</span>";
										else
											$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
							}
					}
					if($this->page){
						//siguiente button
						if ($this->page < $counter - 1)
								$this->pagination .= "<a href=\"".$this->get_pagenum_link($next)."\" class=\"pagedir next\">$n</a>";
							else
								$this->pagination .= "<span class=\"pagedir disabled\">$n</span>";
							if($this->showCounter)$this->pagination .= "<div class=\"pagedir pagination_data\">($this->total_pages Pages)</div>";
					}
				}
				return true;
			}
	}
?>