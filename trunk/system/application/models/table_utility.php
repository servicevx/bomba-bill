<?php
	/** 
	* parse_utility
	* 
	* Utility model that has a variety
	* of useful parsing functions and
	* definitions we don't want in our
	* controllers. Warning. Some comments
	* Out of date :)
	* @author	Dryw Paulic
	* @link	http://port-0.com 
	*/ 
class table_utility extends Model {
 
	
	/** 
	* Creates a Table Template
	* 
	* @access public
	* @param	none
	*/ 
	function set_view_template()	{
	
		//Create a Table Template (http://codeigniter.com/user_guide/libraries/table.html)
		$tmpl = array (
                    'table_open'          => '<table border="1" cellpadding="3" cellspacing="0" >',

                    'heading_row_start'   => '<center><tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<th bgcolor="#FFCA99">',
                    'heading_cell_end'    => '</th>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td bgcolor="#CCCCCC">',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td bgcolor="#CCCCCC">',
                    'cell_alt_end'        => '</td></center>',

                    'table_close'         => '</table>'
					);
		//Set Template
		$this->table->set_template($tmpl); 
	}
	
		function set_form_template()	{

		$tmpl = array (
                    'table_open'          => '<table border="1" cellpadding="3" cellspacing="0" ',

                    'heading_row_start'   => '<center><tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<th bgcolor="#FFCC99">',
                    'heading_cell_end'    => '</th>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td bgcolor="#CCCCCC">',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td bgcolor="#CCCCCC" >',
                    'cell_alt_end'        => '</td>',

                    'table_close'         => '</table></center>'
					);
		//Set Template
		$this->table->set_template($tmpl); 
	}
	
	/** 
	* Removes items in array "ignored"
	* from array "tables". It matches based
	* off the array VALUES, not the key.
	* @access	public
	* @param	array
	* @param	array
	* @return	array
	*/ 
	function  filter_array_values ($tables, $ignore)	{
	
		foreach ($tables as $key => $row)	{
			
			if(in_array($key, $ignore) )	{
				//echo "I found " . $row . " @ Table Array ID :  " . $key ."<br>"; // DEBUG
				unset ($tables[$key]);
			}
			
			elseif(in_array($row, $ignore) )	{
				//echo "I found " . $row . " @ Table Array ID :  " . $key ."<br>"; // DEBUG
				unset ($tables[$key]);
			}
		}

		return $tables;
		
	}
	
	/** 
	* Builds table head for view
	* @access	public
	* @param	array
	* @param	array
	* @return	none
	*/ 
	function  build_view_head ($tables, $ignore)	{
			$result = $this->filter_array_values($tables, $ignore);
			$this->table->set_heading($result);
	}
	
	/** 
	* Builds anchor links named after
	* the project codename and filters
	* out ignored fields
	*
	* @access public
	* @param	list
	* @param	array
	* @return	none
	*/ 
	function build_view_rows($query, $ignore, $anchor_field, $anchor_id)	{
		
		foreach ($query->result_array() as $row)	{
			//Take project ID and turn it into an Anchor link. Display codename field as anchor name.
			$row[$anchor_field] = anchor('bomba_bill/bom_view/'.$row[$anchor_id], $row[$anchor_field]);
		
			//Don't display rows in our $ignore list
			foreach ($ignore as $values)	{
				unset ($row[$values]);
			}
			$this->table->add_row($row);	
		} 
	}
	
	/** 
	* Builds Form Rows
	*
	* @access public
	* @param	array
	* @param	array
	* @param	array
	* @param	string
	* @param	string
	* @param	string
	*/ 
	function build_bom_rows($part_tables, $part_ignore, $items, $anchor_field, $anchor_id, $project_id)	{
			
		$result = $this->filter_array_values($part_tables, $part_ignore);
			
			foreach($items as $key => $value) {
				if($value == "") {
				unset($items[$key]);
				}
			} 
	
			//Create Form Input
			foreach ($items as $key=> $values)	{
				
				$query1 = $this->db->select($result)->where('id', $key)->get('bom_item');
				
				if ($query1->num_rows() > 0) {
					foreach ($query1->result_array() as $row)	{
					
						//Create Anchor ID's
						$row[$anchor_field] = anchor('bomba_bill/assembly_view/'.$project_id."/" . $key, $row[$anchor_field]);
					
						//Splice array and insert quantity values
						$row_start= array_splice($row, 0, 4, $values);
					
						//Merge array back with original (extra key values overwritten by first arguement)
						$item_row = array_merge($row_start, $row);
						$this->table->add_row($item_row);		
					
					}
				}
				else	{
				
				$row = "This Item does not have a BOM";
				$this->table->add_row($row);		
				
				}
				
			}	
	}
	
	/** 
	* Builds Form Rows
	*
	* @access public
	* @param	array
	* @param	array
	* @return	none
	*/ 
	function build_form_rows($form_tables, $form_ignore, $submit_button)	{
			
		$result = $this->filter_array_values($form_tables, $form_ignore);
		
			//Create Form Input
			foreach ($result as $row)	{
			
				$form_row= $row . "<td bgcolor=#CCCCCC>". form_input($row, '') . "</td>" ;
				$this->table->add_row($form_row);
			
			}
			
		$this->table->add_row("<td bgcolor=#CCCCCC>". form_button($submit_button) . "</td>" );
		
	}
	

	
	function build_crumbs($parent_id, $project_id, $crumbs)	{
	
		$query1 = $this->db->select('id, parent_id, item_name')->where('id', $parent_id)->get('bom_item');
		$row = $query1->row_array();
		$first = "";
	
			if($parent_id > 0)	{
			
				$crumbs = $this->build_crumbs($row['parent_id'], $project_id, $crumbs);
			}
		
			if( ISSET($row['item_name']) )	{
			
					if ($row['item_name']  == "")	{
					
					$first = $row['item_name'];
				
				}
				else	{
				
					$crumbs = anchor('bomba_bill/assembly_view/'.$project_id."/" .$row['id'], $row['item_name']) ."-->". $first;
				}
			}
	
	return print_r($crumbs);
	}

	
}
/* End of file parse_utility.php */
/* Location: ./system/application/models/parse_utility.php */