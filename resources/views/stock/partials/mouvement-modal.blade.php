{{-- Modal Mouvement de Stock --}}
<div class="modal fade" id="mouvementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white border-0 py-3"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-exchange-alt me-2"></i> Enregistrer un mouvement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('stock.mouvements.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Pièce concernée <span
                                class="text-danger">*</span></label>
                        <select name="piece_id" class="form-select border-0 bg-light" required>
                            <option value="">-- Sélectionner une pièce --</option>
                            @foreach($pieces as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }} (Réf: {{ $p->reference ?? 'N/A' }}) - Stock:
                                    {{ $p->quantite }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Type de mouvement <span
                                    class="text-danger">*</span></label>
                            <select name="type" class="form-select border-0 bg-light" required>
                                <option value="ENTREE">ENTRÉE (+)</option>
                                <option value="SORTIE">SORTIE (-)</option>
                                <option value="AJUSTEMENT">AJUSTEMENT (+/-)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Quantité <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="quantite" class="form-control border-0 bg-light" min="1"
                                value="1" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-uppercase">Motif / Commentaire</label>
                        <textarea name="motif" class="form-control border-0 bg-light" rows="2"
                            placeholder="Ex: Réapprovisionnement, erreur d'inventaire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">VALIDER LE
                        MOUVEMENT</button>
                </div>
            </form>
        </div>
    </div>
</div>