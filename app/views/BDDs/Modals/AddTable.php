<div class="modal fade" id="myModalAddTable">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?p=table.add" method="post" id="formTableAdd">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Création d'une table</h4>
                </div>
                <div class="modal-body">
                    <p>Insérer le nom de la nouvelle table :</p>
                    <div class="form-group">
                        <input type="text" style="width:100%;" name="addTableName" id="TableAddNewInput">
                    </div>
                </div>
                <div>
                    <input type="hidden" name="dbName" value="" id="databaseTableAddInput">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function addTable(bdd) {
        console.log(bdd);
        document.getElementById("databaseTableAddInput").value = bdd;
    }
</script>
