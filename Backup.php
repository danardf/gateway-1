<?php
namespace FreePBX\modules\Gateway;
use FreePBX\modules\Backup as Base;
class Backup Extends Base\BackupBase{
    
	public function runBackup($id,$transaction){
		$config["gateway"] = $this->FreePBX->Gateway->getAllGateway();
		$this->addConfigs($config);
	}
}
