<br><h1 class="title text-center"><?= _("Gateway") ?></h1>
<section class="projects-clean m">
    <div class="container">
        
        <div class="intro">
            <p class="text-center"><?= _("Document to configure the gateway") ?></p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 pt-5">
            <div class="col mb-4">
                <div class="card p-2"><img class="card-img-top w-100 d-block fit-cover" onclick="image(event)" src="<?= $img ?>/assets/img/grid.png" />
                    <div class="card-body p-4">
                        <h3 class="font-weight-bold card-title" style="text-align: center;"><?= _("Main page") ?></h3>
                        <p class="card-text">                            
                            <?= _("When you go to the gateway page, you've got a grid showing all gateways configured like this.") ?><br /><br />
                            <?= _("This grid shows some useful things regarding this gateway.") ?><br /><br />
                        </p>
                        <div class="d-flex"></div>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card p-2"><img class="card-img-top w-100 d-block fit-cover" onclick="image(event)" src="<?= $img ?>/assets/img/add_gateway.png" />
                    <div class="card-body p-4">
                        <h3 class="font-weight-bold card-title" style="text-align: center;"><?= _("Add Gateway") ?></h3>
                        <p class="card-text">
                            <?= _("To add a gateway, click on the + Add button above the grid.")?><br /><br />
                            <?= _("A page is displayed, you just have to fill in the desired fields.") ?><br /><br />                    
                            <?= _("The required fields are:") ?><br /><br />
                                - <strong><?= _("Extension as gateway") ?></strong><br />
                                - <strong><?= _("Gateway") ?></strong><br />
                                - <strong><?= _("Primary DID") ?></strong><br /><br />
                            <?= _("Start by selecting an extension to convert to a gateway.") ?><br /><br />
                            <?= _("Enter the IP address of the gateway that will be connected to the extension. Example: 200.25.46.30 or 200.25.46.30:5061") ?><br /><br />
                            <?= _("If you do not specify the port in the IP address of your gateway, the port will point to 5060.") ?><br /><br />
                            <?= _("When you choose an extension to convert to a gateway, the Primary DID fields will be filled in automatically. The first DID will therefore be the head phone number.") ?><br /><br />
                            <?= _("You may add any DID number associated to the gateway clicking on DID + button. If the field DID is added, fill it, otherwise, you can delete it clicking on the trash button.") ?><br /><br />
                            <?= _("Fill in the other fields to identify the gateway. Then click on the Submit button.") ?><br /><br />
                        </p>
                        <div class="d-flex">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card p-2"><img class="card-img-top w-100 d-block fit-cover" onclick="image(event)" src="<?= $img ?>/assets/img/edit_gateway.png" />
                    <div class="card-body p-4">
                        <h3 class="font-weight-bold card-title" style="text-align: center;"><?= _("Edit Gateway") ?></h3>
                        <p class="card-text">
                            <?= _("On the grid, click on the Pencil icon on the right of row. Same type of fields used above. You can change everything excepted the field: Extension as Gateway and Primary DID.") ?>
                        </p>
                        <div class="d-flex"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
            <div class="col mb-4">
                <div class="card p-2"><img class="card-img-top w-100 d-block fit-cover" onclick="image(event)" src="<?= $img ?>/assets/img/grid.png" />
                    <div class="card-body p-4">
                        <h3 class="font-weight-bold card-title" style="text-align: center;"><?= _("Delete Gateway") ?></h3>
                        <p class="card-text">
                            <?= _("You can delete a gateway clicking on the trash icon.") ?>
                        </p>
                        <div class="d-flex"></div>
                    </div>
                </div>
            </div>
            <div class="col col-xl-8 mb-8">
                <div class="card p-2">
                    <pre class="mermaid card-img-top w-100 d-block fit-cover" style="color: white; text-align: center;">
                        flowchart TD
                        A[Trunk PJSIP] <--> B(Context:  \n from-trunk-gateway)
                        B<--> C{Calls}
                        C<--> D[Context: \n from-internal-gateway\nfa:fa-server\nGW 1]
                        C<--> E[Context: \n from-internal-gateway\nfa:fa-server\nGW 2]
                        C<--> F[Context: \n from-internal-gateway\nfa:fa-server\nPBX 1]
                        D<--> G[fa:fa-phone\nExt 1]
                        D<--> h[fa:fa-phone\nExt 2]
                        E<--> i[fa:fa-phone\nExt 1]
                        E<--> j[fa:fa-phone\nExt 2]
                        F<--> k[fa:fa-phone\nExt 1]
                        F<--> L[fa:fa-phone\nExt 2]
                    </pre>  
                    <div class="card-body p-4">
                        <h3 class="font-weight-bold card-title text-center"><?= _("Setting Up FreePBX or PBXact") ?></h3>
                        <p class="card-text">
                            <?= _("Once extensions (PJSIP) are used as a gateway, their context will change to:") ?> <strong>from-internal-gateway.</strong><br>
                            <?= _("Regarding the trunk (PJSIP) used to receive calls to gateways, set the context manually to:") ?> <strong>from-trunk-gateway.</strong><br>
                            <?= _("Look the schema above.") ?><br><br>
                        </p>
                        <div class="d-flex"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Modal -->
<div id="ModalImage" class="modal">
    <!-- The Close Button -->
    <span class="closeimage" style="padding-top: 30px;">&times;</span>

    <!-- Modal Content (The Image) -->
    <img class="modal-content" id="img01">

    <!-- Modal Caption (Image Text) -->
    <div id="caption"></div>
</div>

<p><br><br><br></p>
<script type="module">
  import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
  mermaid.initialize({ startOnLoad: true });
</script>


