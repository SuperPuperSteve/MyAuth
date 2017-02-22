<?php
namespace Auth\Commands;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class LoginCommand implements CommandExecutor {
	public function __construct(\Auth\Auth $plugin){
		$this->plugin = $plugin;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		/* если авторизирован */
		if($this->plugin->isAuthorized($sender)){
			$sender->sendMessage('§cYou are already authorized!');
			return;
		} 
		
		$db = $this->plugin->getDB();
		(string) $nickname = strtolower($sender->getName());
		
		$info = $db->query("SELECT * FROM `{$this->plugin->config['table_prefix']}pass` WHERE nickname='$nickname'");
		$data = $info->fetch_assoc();
		
		if ($data == null) {
			/* data равно нулю, значит не зарегестрирован */
			$sender->sendMessage('§cYou aren\'t registered yet!');
			return;
		} else {
			 
			 if(!isset($args[0])) {
				 $sender->sendMessage('Type password, please');
				 return;
			 }
			 
			/* попытка авторизации */
			$password = md5($args[0]);
			
			if($data['password_hash'] == $password){
				/* если пароль подошёл */
				$this->plugin->authorize($sender);
				$sender->sendMessage('You\'ve successfully authrized!');
			} else {
				/* если пароль не подошёл */
				$sender->sendMessage('Wrong password!');
			}
		}

	}
}