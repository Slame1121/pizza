<?php
/**
 * Created by PhpStorm.
 * User: slame
 * Date: 27.03.2017
 * Time: 23:27
 */

namespace  Less;
class Less
{

	/**
	 * Css for site
	 * @var string
	 */
	public $cssOut;

	/**
	 * @var \Less_Parser
	 */
	public $less;

	public $config;

	public $registry;

	public $current_dir;

	public function __construct($registry)
	{
		require_once ('Less_Parser.php');
		$this->less = new \Less_Parser();

		$this->registry = $registry;
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->current_dir = DIR_LESS_FILES;
		if(is_file(DIR_APPLICATION.'view/theme/pizza/stylesheet/style.css')){
			$this->cssOut = file_get_contents(DIR_APPLICATION.'view/theme/pizza/stylesheet/style.css');
		}else{
			$this->cssOut = '';
		}

		$this->compileLess($this->current_dir);

	}


	/**
	 * Returned css for site
	 */
	public function getCss(){
		return $this->cssOut;
	}

	/**
	 * Compile less if files were changed
	 * @param $dir
	 * @throws \Exception
	 */
	public function compileLess($dir)
	{

		$less_root = $dir;
		$i = 0;
		$scandir = scandir($less_root);

		while($i < count($scandir)){
			if(is_dir($less_root.$scandir[$i])
				&& $scandir[$i] != '.' && $scandir[$i] != '..'){
				$this->compileLess($less_root.$scandir[$i].'/');
			}


			if(is_file($less_root.$scandir[$i])){
				if(filemtime($less_root.$scandir[$i]) > $this->config->get('config_LAST_EDIT_CSS_FILE')){
					try{
						$this->less->parseFile(DIR_LESS_FILES.'main.less');
						$this->cssOut = $this->less->getCss();
					}catch(\Exception $e){
						$error_message = $e->getMessage();
						echo $error_message;
						die();
					}
					$this->db->query("UPDATE " . DB_PREFIX . "setting SET value=". time() ." WHERE `key` = 'config_LAST_EDIT_CSS_FILE'");
					file_put_contents(DIR_APPLICATION.'view/theme/pizza/stylesheet/style.css' ,$this->cssOut);
				}
			}
			$i++;
		}
	}

}