{{-- Modal Import Excel --}}
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-content-ventes">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="importExcelModalLabel">📊 Importer des ventes (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ventes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <div class="input-group">
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                La taille maximale du fichier est de 4 Mo.
                            </small>
                            <a href="{{ route('ventes.template') }}" class="fw-bold small text-decoration-none text-primary">
                                <i class="fas fa-file-download me-1"></i> Télécharger le modèle (.xlsx)
                            </a>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-4 border">
                        <label class="fw-bold small text-primary mb-2 d-block"><i class="fas fa-info-circle me-1"></i> Structure attendue des colonnes :</label>
                        <div class="bg-white p-2 rounded-3 border mb-2 text-center modal-structure-cols">
                            type | imei | modele | client_nom | date_vente | duree_garantie_mois | reference_produit | numero_facture_vente
                        </div>
                        <div class="text-muted modal-info-text">
                            * **Ligne d'en-tête obligatoire** : La première ligne doit contenir les noms exacts des colonnes (ex: `imei`, `modele`, `date_vente`, etc.)<br>
                            * **Format de date** : Format standard de date Excel ou `AAAA-MM-JJ`<br>
                            * **Unicité** : L'IMEI est la clé d'identification. Si l'IMEI existe déjà, sa fiche de vente sera mise à jour avec les nouvelles valeurs.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm text-uppercase btn-import-excel">
                        <i class="fas fa-upload me-2"></i> Importer Excel
                    </button>
                    <button type="button" class="btn btn-light px-3 rounded-pill fw-bold" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
