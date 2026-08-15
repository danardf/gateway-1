<?php
/**
 * https://bootstrap-table.com/docs/api/table-options/
 */
?>
<input type="hidden" id="message" value="<?= !empty($message) ? htmlspecialchars($message, ENT_QUOTES, 'UTF-8') : "" ?>">

<div id="toolbar-all">
	<a href="?display=gateway&action=add_gateway"><button title="<?= _("Add a gateway") ?>" class="btn btn-default"><i class="fa fa-plus"></i>&nbsp;<?= _("Add") ?></button></a>
</div>

<table	id="gatewayList" data-url="ajax.php?module=gateway&command=gatewayList"
		data-show-refresh="true"
		data-toolbar="#toolbar-all"
		data-show-columns="true"
		data-show-toggle="true"
		data-toggle="table"
		data-pagination="true"
		data-search="true"
		data-escape="true"
		data-detail-view="true"
		data-detail-formatter="detailFormatter"
		class="table table-striped">
	<thead>
		<tr>
			<th data-field="extension" data-escape="true" data-width="150" data-sortable="true"><?= _("Phone Number") ?></th>
			<th data-field="accountcode" data-escape="true" data-sortable="true"><?= _("Account Code") ?></th>
			<th data-field="description" data-escape="true" data-sortable="true"><?= _("Description") ?></th>
			<th data-field="contact" data-escape="true" data-sortable="true"><?= _("Contact") ?></th>
			<th data-field="city" data-escape="true" data-width="200" data-sortable="true"><?= _("City") ?></th>
			<th data-field="gateway" data-escape="true" data-width="200" data-sortable="true"><?= _("Gateway") ?></th>
			<th data-field="status" data-formatter="statusFormatter" data-width="80" data-align="center" data-sortable="true"><?= _("Status") ?></th>
			<th data-field="dids" data-formatter="didsFormater" data-escape="true" data-width="60" data-align="center" data-sortable="true"><?= _("DIDs") ?></th>			
		<th data-field="id" data-formatter="extensionsFormater" data-width="100" data-align="center"><?= _("Action") ?></th>
		</tr>
	</thead>
</table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jed/1.1.1/jed.min.js"></script>
<script>
	var i18n = new Jed(<?= !empty($jsloc) ? $jsloc : "[]" ?>);
</script>
