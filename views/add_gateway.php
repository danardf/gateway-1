<?php $formdata = $formdata ?? []; ?>
<input type="hidden" id="message" value="<?= !empty($message) ? htmlspecialchars($message, ENT_QUOTES, 'UTF-8') : "" ?>">
<form class="fpbx-submit" method="post" action="?display=gateway">
<input type="hidden" name="edit" value="no">

<!--Extension as Gateway-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="extension"><?= _("Extension as Gateway") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="extension"></i>
					</div>
					<div class="col-md-9">
						<select name="extension" id="extension" class="form-control">
							<?php foreach($users as $user): ?>
								<option value="<?= $user['extension'] ?>" <?= (isset($formdata['extension']) && $formdata['extension'] == $user['extension']) ? 'selected' : '' ?>><?= $user['extension'] ?> - <?= $user['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="extension-help" class="help-block fpbx-help-block"><?= _("Select an extension.") ?></span>
		</div>
	</div>
</div>
<!--END Extension as Gateway-->

<!--Contact-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="contact"><?= _("Contact") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="contact"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+" required class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($formdata['contact'] ?? '') ?>" placeholder="<?= _("Enter the name of the contact.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="contact-help" class="help-block fpbx-help-block"><?= _("Enter the name of the contact.") ?></span>
		</div>
	</div>
</div>
<!--END Contact-->

<!--Description-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="description"><?= _("Description") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="description"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+" class="form-control" id="description" name="description" value="<?= htmlspecialchars($formdata['description'] ?? '') ?>" placeholder="<?= _("Enter the description of the gateway.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="description-help" class="help-block fpbx-help-block"><?= _("Enter the description of the gateway.") ?></span>
		</div>
	</div>
</div>
<!--END Description-->

<!--Email-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="email"><?= _("Email") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="email"></i>
					</div>
					<div class="col-md-9">
						<input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($formdata['email'] ?? '') ?>" placeholder="<?= _("Enter the email of the contact.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="email-help" class="help-block fpbx-help-block"><?= _("Enter the email of the contact.") ?></span>
		</div>
	</div>
</div>
<!--END Email-->

<!--Address-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="address"><?= _("Address") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="address"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+" class="form-control" id="address" name="address" value="<?= htmlspecialchars($formdata['address'] ?? '') ?>" placeholder="<?= _("Enter the address of the contact.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="address-help" class="help-block fpbx-help-block"><?= _("Enter the address of the contact.") ?></span>
		</div>
	</div>
</div>
<!--END Address-->

<!--Zip Code-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="zip"><?= _("Zip code") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="zip"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="\d{3,6}" class="form-control" id="zip" name="zip" value="<?= htmlspecialchars($formdata['zip'] ?? '') ?>" placeholder="<?= _("Enter the ZIP code.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="zip-help" class="help-block fpbx-help-block"><?= _("Enter the ZIP code.") ?></span>
		</div>
	</div>
</div>
<!--END Zip Code-->

<!--City-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="city"><?= _("City") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="city"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+" class="form-control" id="city" name="city" value="<?= htmlspecialchars($formdata['city'] ?? '') ?>" placeholder="<?= _("Enter the city of the contact.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="city-help" class="help-block fpbx-help-block"><?= _("Enter the city of the contact.") ?></span>
		</div>
	</div>
</div>
<!--END City-->

<!--Country-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="country"><?= _("Country") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="country"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[a-zA-ZéèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+" class="form-control" id="country" name="country" value="<?= htmlspecialchars($formdata['country'] ?? '') ?>" placeholder="<?= _("Enter the country of the contact.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="country-help" class="help-block fpbx-help-block"><?= _("Enter the country of the contact.") ?></span>
		</div>
	</div>
</div>
<!--END Country-->

<!--Gateway IP-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="gateway"><?= _("Gateway IP") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="gateway"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="^(\d{1,3}\.){3}\d{1,3}(:\d{1,5})?$" required class="form-control" id="gateway" name="gateway" value="<?= htmlspecialchars($formdata['gateway'] ?? '') ?>" placeholder="<?= _("Ip address of the remote gateway. (Public IP address)") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="gateway-help" class="help-block fpbx-help-block"><?= _("Enter the IP address of the gateway that will be connected to the extension. Example: 200.25.46.30 or 200.25.46.30:5061") ?></span>
		</div>
	</div>
</div>
<!--END Gateway IP-->

<!--Account Code-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="accountcode"><?= _("Account Code") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="accountcode"></i>
					</div>
					<div class="col-md-9">
						<input type="text" pattern="[A-Za-z0-9_\-]{1,40}" class="form-control" id="accountcode" name="accountcode" value="<?= htmlspecialchars($formdata['accountcode'] ?? '') ?>" placeholder="<?= _("Enter the account code.") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="accountcode-help" class="help-block fpbx-help-block"><?= _("Enter the account code.") ?></span>
		</div>
	</div>
</div>
<!--END Account Code-->

<!--Call Limit-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="call_limit"><?= _("Call Limit") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="call_limit"></i>
					</div>
					<div class="col-md-9">
						<input type="number" min="0" class="form-control" id="call_limit" name="call_limit" value="<?= htmlspecialchars($formdata['call_limit'] ?? '0') ?>" placeholder="<?= _("0 = unlimited") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="call_limit-help" class="help-block fpbx-help-block"><?= _("Maximum number of simultaneous calls (0 = unlimited)") ?></span>
		</div>
	</div>
</div>
<!--END Call Limit-->

<!--DID Base-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="primarydid"><?= _("DID Base") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="primarydid"></i>
					</div>
					<div class="col-md-9">
						<input type="tel" pattern="\d{1,15}" required class="form-control" id="primarydid" name="dids[]" value="<?= htmlspecialchars($formdata['dids'][0] ?? '') ?>" placeholder="<?= _("Primary DID number to afect on this gateway (Usualy the same as your extension number).") ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="primarydid-help" class="help-block fpbx-help-block"><?= _("Primary DID number to afect on this gateway (Usualy the same as your extension number).") ?></span>
		</div>
	</div>
</div>
<!--END DID Base-->

<!--Additional DIDs-->
<div class="element-container">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label"><?= _("Additional DIDs attached to your gateway") ?></label>
					</div>
					<div class="col-md-9">
						<button type="button" class="btn btn-default" id="addDID"><i class="fa fa-plus"></i>&nbsp;<?= _("DID") ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<span id="dids-help" class="help-block fpbx-help-block"><?= _("Additional DIDs attached to your gateway") ?></span>
		</div>
	</div>
</div>
<!--END Additional DIDs-->

<div id="dids">
<?php if(!empty($formdata['dids'])): ?>
<?php foreach($formdata['dids'] as $index => $did): ?>
	<?php if($index === 0) continue; ?>
<div class="did-row">
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label"><?= _('DID') ?></label>
						</div>
						<div class="col-md-9 input-group">
							<input type="tel" pattern="\d{1,15}" required name="dids[]" class="form-control" value="<?= htmlspecialchars($did) ?>" placeholder="<?= _("DID number to add on this gateway (only one entry).") ?>">
							<span class="input-group-btn"><button type="button" class="btn btn-danger delDid" title="<?= _('Delete it') ?>"><i class="fa fa-trash"></i></button></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

</form>

<script>
$(document).ready(function(){
	var i18n = new Jed(<?= $jsloc ?>);

	// When redisplaying the form after a failed submission, keep what the
	// user typed instead of letting updateDID() overwrite it from the
	// (already correctly preselected) extension dropdown.
	<?php if(empty($formdata)): ?>
	updateDID();
	<?php endif; ?>

	$("#extension").change(function(){
		updateDID();
	});

	$("#addDID").click(function(){
		var input = '<div class="did-row">';
		input += '<div class="element-container">';
		input += '<div class="row"><div class="col-md-12"><div class="row"><div class="form-group">';
		input += '<div class="col-md-3"><label class="control-label">' + i18n.gettext('DID') + '</label></div>';
		input += '<div class="col-md-9 input-group">';
		input += '<input type="tel" pattern="\\d{1,15}" required name="dids[]" class="form-control" placeholder="' + i18n.gettext("DID number to add on this gateway (only one entry).") + '">';
		input += '<span class="input-group-btn"><button type="button" class="btn btn-danger delDid" title="' + i18n.gettext('Delete it') + '"><i class="fa fa-trash"></i></button></span>';
		input += '</div></div></div></div></div>';
		input += '</div></div>';
		$('#dids').append(input);
	});
});
</script>
