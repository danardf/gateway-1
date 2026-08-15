<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
$dispnum = "gateway";
$request = freepbxGetSanitizedRequest();
$request["action"] = empty($request["action"]) ? "" : $request["action"];
$heading = _("Gateway List");

switch($request["action"]){
	case "add_gateway":
		$heading = _("Gateway") . ": " . _("Add");
	break;
	case "edit_gateway":
		$heading = _("Gateway") . ": " . _("Edit") . " " . ($request["gateway"] ?? "");
	break;
	case "help":
		$heading = _("Gateway");
	break;
}

$content = FreePBX::Gateway()->showPage();
?>
<div class="container-fluid">
	<h1 class="title mt-3"><?php echo $heading ?></h1>
	<div class="display">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<div class="display full-border p-3 mt-3">
						<?= $content ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
