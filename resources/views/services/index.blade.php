{{-- Nom : services/index.blade.php | Rôle : Gestion de la grille tarifaire (Services et Prix) --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Gestion des services</h2>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-circle me-2"></i>Nouveau Service
        </button>
    </div>

    {{-- Boîte de dialogue (Modal) pour créer un nouveau tarif --}}
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Nouveau Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du Service</label>
                            <input type="text" name="libelle" class="form-control rounded-3" placeholder="Ex: Lavage Express" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix Unitaire (FCFA)</label>
                            <input type="number" name="prix_unitaire" class="form-control rounded-3" placeholder="Ex: 1500" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unité de mesure</label>
                            <select name="unite" class="form-select rounded-3">
                                <option value="kg">Kilo (KG)</option>
                                <option value="paire">Paire (Chaussures)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Créer le service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            {{-- Tableau récapitulatif des services disponibles --}}
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Service</th>
                        <th>Prix Unitaire</th>
                        <th>Unité</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $service->libelle }}</td>
                        <td>{{ number_format($service->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge {{ $service->unite == 'kg' ? 'bg-info' : 'bg-warning' }} rounded-pill">
                                {{ strtoupper($service->unite) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill me-2" 
                                    onclick="editService({{ $service->id }}, '{{ addslashes($service->libelle) }}', {{ $service->prix_unitaire }}, '{{ $service->unite }}')">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Supprimer ce tarif ?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Modifier le Tarif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du Service</label>
                        <input type="text" name="libelle" id="edit_name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix Unitaire (FCFA)</label>
                        <input type="number" name="prix_unitaire" id="edit_price" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unité de mesure</label>
                        <select name="unite" id="edit_unit" class="form-select rounded-3">
                            <option value="kg">Kilo (KG)</option>
                            <option value="paire">Paire (Chaussures)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editService(id, name, price, unit) {
        // On change l'URL de l'action du formulaire dynamiquement
        document.getElementById('editForm').action = "/services/" + id;
        
        // On remplit les champs de la modale
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_unit').value = unit;
        
        // On affiche la modale
        var myModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
        myModal.show();
    }
</script>
@endsection