<?php
	/** 
	* bomba_bill 
	* 
	* Herein Lies The Source
	* of bomba_bill. Beware
	* There may be dragons.
	* @package bomba_bill
	* @category	PMS
	* @author	Dryw Paulic
	* @link	http://port-0.com 
	*/ 
class bomba_bill extends Controller	{

	/** 
	* Main Controller for Bomba Bill Bom Manager
	* Mostly generates information for use by table_utils model 
	* and related views.
	* @access	public
	* @param	none
	* @return	none
	*/ 
	function bomba_bill()	{
		parent::Controller();
	}
	
	function index()	{
		
		// ------------- CONFIGURABLE SECTION -----------------------------------
		//Set Main Table lists
		$project_view_tables = array("id", "codename", "description", "bom_head", "entry_date" );
		
		//Set Ignore Arrays
		$project_view_ignore = array("bom_head", "id"); //Used for main table
		$project_form_ignore = array("bom_head", "id", "entry_date"); //Used for form
		
		//Set Anchor Field and ID . Purpose is to autogenerate the anchor links in the format
		//$row[$anchor_field] = anchor('bomba_bill/bom_view/'.$row[$anchor_id], $row[$anchor_field]);
		$anchor_field = "codename";
		$anchor_id = "id";
		
		//Form Submit Button Data
		$project_form_submit_button = array(
			'type' => 'submit',
			'content' => 'Create Project'
		);
		
		// -------------------- END OF CONFIGURABLE SECTION ---------------
		
		// -------------------- Variable Declaration ---------------
		$bom_id = 1; //If all else fails, this is set to 1, which is necessary for when the database has no rows in it.
		
		// -------------------- Mysql Queries ---------------
		$query = $this->db->select($project_view_tables)->get('projects');
		
		// -------------------- Query manipulation ---------------
		foreach ($query->result_array() as $row)	{
			$bom_id = $row['id'] +1;
		}
		
		// -------------------- Table Generation ---------------
		$this->table_utility->set_view_template(); //Set Table Template
	
		$this->table_utility->build_view_head($project_view_tables, $project_view_ignore); //Build columns of table
		$this->table_utility->build_view_rows($query, $project_view_ignore, $anchor_field, $anchor_id); // Build rows of table
		$project_table = $this->table->generate(); //Generate Table
		$this->table->clear(); //Clear table, so that we can build another
		
		$this->table_utility->build_form_rows($project_view_tables, $project_form_ignore, $project_form_submit_button); //Build Form 
		$project_form_table = $this->table->generate(); //Generate Form

		// -------------------- View Elements ---------------
		// Elements to pass on to view
		$data['title'] = "Bomba-Bill  Manager Alpha";
		$data['project_table'] = $project_table; //Used to main table in view
		$data['form_table'] = $project_form_table; //Used to create form in view
		$data['bom_id'] = $bom_id; //Used as hidden value in forms, to allow a part to know to which project it belongs
		$data['todo'] = array( 'Create Index Function', 'Create index view'); // Neverending Todo :D
		
		$this->load->view('project_view.php', $data);
	}
	
	function bom_view()	{
		
		// ------------- CONFIGURABLE SECTION -----------------------------------
		//Set Main Table lists
		$head_tables = array("id", "item_id", "quantity"); //Used for selects from bom_head
		$project_tables =array("id", "codename", "description", "bom_head", "entry_date");//Used for selects from projects
		$part_tables = array("id", "item_name", "parent_id", "type", "service", "description", "costing", "quantity", "manufacturer", "vendor", "weight", "notes"); //Used for selects from bom_item
		
		//Set Anchor Field and ID .
		$bom_anchor_field = "item_name"; 
		$bom_anchor_id = "id";
		
		//Set Ignore arrays
		$project_form_ignore = array("bom_head", "id", "entry_date"); //Used to Generate Columns for Top Level Part View and the Add New Part Form
		$part_form_ignore  = array("id", "parent_id", "type");  //Used to Generate Columns for Top Level Part View and the Add New Part Form
		
		$part_view_ignore = array( "id", "parent_id", "type", "bom_head", "item_id", "quantity"); //Used to Generate rows for Top level Part View
		$part_quantity_ignore = array("quantity"); //Used to specifically ignore quantity field.
		
		//Project Update button!
		$project_submit_button = array(
			'type' => 'submit',
			'content' => 'Update Project'
		);
		
		//Part Create Button
		$part_submit_button = array(
			'type' => 'submit',
			'content' => 'Create Part'
		);
		
		// -------------------- END OF CONFIGURABLE SECTION ---------------
		
		// -------------------- Variable Declaration ---------------
		$item_id = 1; //If all else fails, this is set to 1, which is necessary for when the database has no rows in it.
		$bom_id = $this->uri->segment(3); //Grab bom/project id form URI
		$project_codename = '';
		$part_query = $this->table_utility->filter_array_values($part_tables, $part_quantity_ignore); //Normally table utils does this automatically, but I need a filtered list here.
	
		// -------------------- Mysql Queries ---------------
		$query1 = $this->db->select($project_tables)->where('id', $bom_id)->get('projects'); //Used to get Data about Project
		$query2 = $this->db->select($head_tables)->where('id', $bom_id)->get('bom_head'); //Used to get Bom Data (Items id and quantity)
		$query3 = $this->db->select('id')->get('bom_item');  //Used to get last item_id in table, so we can predict next auto-increment value
		$query4 = $this->db->select($part_query)->get('bom_item'); //Used to get a list of all parts in database
		
		// -------------------- Query manipulation ---------------
		foreach ($query1->result_array() as $row)	{
		
			$project_codename = $row['codename']; //get project codename so we can pass it on to view later
			
		}
		
		foreach ($query2->result_array() as $row)	{
		
			$item_id_string = $row['item_id']; //Extract item_id's from bom_head
			$item_qty_string = $row['quantity']; //Extract  quantity from bom_head
			
		}
		
		$id_array = explode("&", $item_id_string );  //Turn delimited string into an array
		$qty_array = explode("&", $item_qty_string );
		$items_array = array_combine($id_array, $qty_array); //Combine arrays, $id becomes the $key and $qty the value. Gets passed to Top level Bom Table.
		
		foreach ($query3->result_array() as $row)	{
			
				$item_id = $row['id'] +1; //Predict next item_id for view forms
		}
		
		// -------------------- Table Generation ---------------
		//Build Top Level Part View
		$this->table_utility->set_view_template();
		//Build Columns. Uses $part_form_ignore since the columns will need the 'quantity' field.
		$this->table_utility->build_view_head($part_tables, $part_form_ignore ); 
		 //Build Columns. Uses $part_view_ignore, since quantity field will be spliced in via the table utils automatically.
		$this->table_utility->build_bom_rows($part_tables, $part_view_ignore, $items_array, $bom_anchor_field, $bom_anchor_id, $bom_id);
		$bom_table = $this->table->generate(); //Generate Table
		$this->table->clear(); //Clear Table
		
	/*	//Build Table of ALL existing parts in the database
		$this->table_utility->build_view_head($part_tables, $part_view_ignore ); //Use $part_view_ignore so table looks like Top Level table.
		$this->table_utility->build_view_rows($query4 , $part_view_ignore, $bom_anchor_field, $bom_anchor_id); //Build Rows
		$view_table = $this->table->generate(); //Generate
		$this->table->clear(); //Clear Table
	*/
		//Build Forms
		$this->table_utility->set_form_template();
		//Build Edit Project Form
		$this->table_utility->build_form_rows($project_tables, $project_form_ignore, $project_submit_button); 
		$project_form_table = $this->table->generate(); 
		$this->table->clear();
	
		//Build Add Part Form
		$this->table_utility->build_form_rows($part_tables, $part_form_ignore, $part_submit_button);
		$part_form_table = $this->table->generate(); 
		$this->table->clear();
		
		// -------------------- View Elements ---------------
		$data['title'] = "Bomba-Bill  Manager Alpha";
		$data['bom_table'] = $bom_table;  //Used to show list of top level parts in this BOM
		//$data['view_table'] = $view_table; //Used to show list of top level parts in this BOM
		$data['part_form_table'] = $part_form_table; //Used to create Add New Part form
		$data['project_form_table'] = $project_form_table; //Edit project details form
		$data['bom_id']	   = $bom_id; //For use as hidden form data
		$data['item_id']	   = $item_id;//For use as hidden form data
		$data['project_codename'] = $project_codename; //For Navigation
		
		$this->load->view('bom_view.php', $data);
		
	}
	
	// This function could probably be coded into the bom_view without issue: TODO
	function assembly_view()	{
	
		// ------------- CONFIGURABLE SECTION -----------------------------------
		//Set Main Table lists
		$head_tables = array("id", "project_list", "item_id", "quantity");
		$project_tables =array("id", "codename", "description", "bom_head", "entry_date");
		$part_tables = array("id", "item_name", "parent_id", "type", "service", "description", "costing", "quantity", "manufacturer", "vendor", "weight", "notes");
		
		//Set Anchor Field and ID . Purpose is to autogenerate the anchor links: $row[$anchor_field] = anchor('bomba_bill/bom_view/'.$row[$anchor_id], $row[$anchor_field]);
		$bom_anchor_field = "item_name";
		$bom_anchor_id = "id";
		
		//Set Ignore arrays
		$project_form_ignore = array("bom_head", "id", "entry_date");
		$part_form_ignore  = array("id", "parent_id", "type");
		$part_table_ignore = array("id", "parent_id", "type", "bom_head", "item_id", "quantity");
		
		//Project Submit button!
		$project_submit_button = array(
			'type' => 'submit',
			'content' => 'Update Project'
		);
		
		//Part Create Button
		$part_submit_button = array(
			'type' => 'submit',
			'content' => 'Create Part'
		);
		
		// -------------------- END OF CONFIGURABLE SECTION ---------------
		
		// -------------------- Variable Declaration ---------------
		$item_id = 1;
		
		$project_id = $this->uri->segment(3);
		$assembly_id = $this->uri->segment(4);
		$project_codename = '';
		$assembly_name = '';
		
		// -------------------- Mysql Queries ---------------
		$query1 = $this->db->select($project_tables)->where('id', $project_id )->get('projects');
		$query2 = $this->db->select($head_tables)->where('id', $assembly_id)->get('assembly_head');
		$query3 = $this->db->select('id')->get('bom_item');
		$query4 = $this->db->select('item_name')->where('id', $assembly_id)->get('bom_item');
		
		// -------------------- Query manipulation ---------------
		foreach ($query1->result_array() as $row)	{
		
		$project_codename = $row['codename'];
		
		}
		
		foreach ($query2->result_array() as $row)	{
			
			$item_id_string = $row['item_id'];
			$item_qty_string = $row['quantity'];

		}
		
		$id_array = explode("&", $item_id_string );
		$qty_array = explode("&", $item_qty_string );
		
		$items_array = array_combine($id_array, $qty_array);
		
		foreach ($query3->result_array() as $row)	{
			
				$item_id = $row['id'] +1;
		}
		
		foreach ($query4->result_array() as $row)	{
			
				$assembly_name = $row['item_name'];
		}
	
		// -------------------- Table Generation ---------------
		$this->table_utility->set_view_template();
		//Build Top Level Part View
		$this->table_utility->build_view_head($part_tables, $part_form_ignore);
		$this->table_utility->build_bom_rows($part_tables, $part_table_ignore, $items_array , $bom_anchor_field, $bom_anchor_id, 	$project_id);
		$assembly_table = $this->table->generate(); 
		$this->table->clear();
		
		//Build Forms
		$this->table_utility->set_form_template();
		//Build Add Part Form
		$this->table_utility->build_form_rows($part_tables, $part_form_ignore, $part_submit_button);
		$part_table = $this->table->generate(); 
		$this->table->clear();
		
		// -------------------- View Elements ---------------
		$data['title'] = "Bomba-Bill  Manager Alpha";
		$data['assembly_table'] = $assembly_table;
		$data['assembly_name'] = $assembly_name;
		$data['part_table'] = $part_table;
		$data['assembly_id']	   = $assembly_id ;
		$data['item_id']	   = $item_id;
		$data['project_codename'] = $project_codename;
		$data['project_id'] = $project_id;

		$this->load->view('assembly_view.php', $data);
	}
	
	
	function create_project()	{
		$data = array(
               'item_id' => '' ,
               'quantity' => '' ,
            );
			
		$this->db->insert('projects', $_POST);
		$this->db->insert('bom_head', $data);
		redirect(base_url());
		
	}
	
	function edit_project()	{
			
		$this->db->where('id', $_POST['bom_head'] )->update('projects', $_POST);
		redirect($_SERVER['HTTP_REFERER'] );
		
	}
	
	function add_top_part()	{
			
		//Extract Bom_head Data from $_POST and delimit it accordingly
		//as per name, this is used to insert data into the bom_head table
		$bom_head_data = array(
			'id' => $_POST['bom_head']   ,
            'item_id' =>     $_POST['item_id'] . "&",
            'quantity' =>    $_POST['quantity'] . "&",
         );
		
		//All parts are potentially assemblies, so create a assembly
		// in the assembly_table with the project_list field populated
		$assembly_data = array(
			'project_list' =>$_POST['bom_head'] . "&",
            'item_id' => '' ,
            'quantity' => '' ,
        );
			
		$post_ignore= array("bom_head", "item_id", "quantity"); //Fields to ignore for the bom_item table
		$bom_item_data = $this->table_utility->filter_array_values($_POST, $post_ignore); //Filter out ignored
		
		//Insert the Data
		$this->db->insert('bom_item', $bom_item_data ); //Insert relevent data in the bom_item field.
		$this->db->insert('assembly_head', $assembly_data); //Insert blank assembly into assembly_head field
		
		//If this inserted item is part of another assembly, update that assembly.
		foreach ($bom_head_data  as $key => $value)	{
		
			if ($key != 'id')	{
				
				$this->db->query("UPDATE bom_head SET " .$key. "= CONCAT(" .$key. "," ."'".$value."'".  ") WHERE id =" .$bom_head_data['id']. ";");
			}
		}
		
		redirect($_SERVER['HTTP_REFERER'] );
		
	}
	
	//Again, could be added into above function: TODO
	function add_assembly_part()	{
	
	$query1 = $this->db->select('project_list')->where('id', $_POST['parent_id'] )->get('assembly_head');
	
	foreach ($query1->result_array() as $row)	{
			
			$project_list = $row['project_list'];
		}
	
	$assembly_head_data = array(
			'id' => $_POST['parent_id']   ,
            'item_id' =>     $_POST['item_id'] . "&",
            'quantity' =>    $_POST['quantity'] . "&",
         );
		 
	$assembly_data = array(
			'project_list' => $project_list,
			'item_id' => '' ,
            'quantity' => '' ,
        );
	
		//Unset bom_head data and then re-index the array.
		//This is the data that will get inserted into the bom_item table.
		$post_ignore= array("bom_head", "item_id", "quantity");
		$bom_item_data = $this->table_utility->filter_array_values($_POST, $post_ignore);
		
		//Insert the Data
		$this->db->insert('bom_item', $bom_item_data );
		$this->db->insert('assembly_head', $assembly_data);
		
		//Loop through the head data, avoiding values we don't want
		foreach ($assembly_head_data  as $key => $value)	{
		
			if ($key != 'id')	{
				
				$this->db->query("UPDATE assembly_head SET " .$key. "= CONCAT(" .$key. "," ."'".$value."'".  ") WHERE id =" .$assembly_head_data['id']. ";");		
				
			}
		}
	redirect($_SERVER['HTTP_REFERER'] );
	}
	
	
}
/* End of file bomba_Bill.php */
/* Location: ./system/application/controllers/bom_manager.php */
