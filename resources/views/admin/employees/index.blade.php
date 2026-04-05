@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2 class="fw-bold"><i class="fas fa-users-cog me-2 text-primary"></i>Gestion des Employés</h2>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fas fa-user-plus me-2"></i>Ajouter un employé
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="fas fa-check-circle fs-4 me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background-color: #ffffff;">
        <div class="card-header border-0 pb-0 pt-4 px-4 bg-white">
            <h5 class="fw-bold mb-0 text-dark">Liste du Personnel</h5>
            <p class="text-muted small">Gérez les accès et les informations de vos employés.</p>
        </div>
        <div class="table-responsive px-2 pb-2">
            <table class="table table-hover align-middle mb-0 border-top mt-2">
                <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-bottom-0">Employé</th>
                        <th class="py-3 border-bottom-0">Contact</th>
                        <th class="py-3 border-bottom-0">Rôle</th>
                        <th class="py-3 border-bottom-0 text-center">Statut</th>
                        <th class="pe-4 py-3 border-bottom-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    <tr>
                        <td class="ps-4 py-4 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-soft-primary text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                    {{ strtoupper(substr($emp->prenom, 0, 1) . substr($emp->nom, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $emp->nom }} {{ $emp->prenom }}</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Rejoint le {{ $emp->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 border-bottom">
                            <div class="text-dark fw-medium"><i class="fas fa-envelope text-primary opacity-75 me-2" style="width: 16px;"></i>{{ $emp->email }}</div>
                            <div class="text-muted mt-1"><i class="fas fa-phone text-secondary opacity-75 me-2" style="width: 16px;"></i>{{ $emp->telephone }}</div>
                        </td>
                        <td class="py-4 border-bottom">
                            <span class="badge bg-soft-{{ $emp->role == 'receptionniste' ? 'primary text-primary' : 'info text-info' }} rounded-pill px-3 py-2 fw-bold border border-{{ $emp->role == 'receptionniste' ? 'primary' : 'info' }} border-opacity-25" style="letter-spacing: 0.5px;">
                                {{ ucfirst($emp->role) }}
                            </span>
                        </td>
                        <td class="py-4 text-center border-bottom">
                            <button type="button" 
                                    class="btn badge bg-{{ $emp->actif ? 'success' : 'danger' }} bg-opacity-10 text-{{ $emp->actif ? 'success' : 'danger' }} border border-{{ $emp->actif ? 'success' : 'danger' }} border-opacity-50 rounded-pill px-4 py-2 toggle-status-btn fw-bold shadow-sm hover-elevate"
                                    data-id="{{ $emp->id }}"
                                    data-url="{{ route('employees.toggle', $emp->id) }}"
                                    style="transition: all 0.3s ease;">
                                {{ $emp->actif ? 'Actif' : 'Inactif' }}
                            </button>
                        </td>
                        <td class="pe-4 text-end py-4 border-bottom">
                            <!-- Bouton Édition -->
                            <button class="btn btn-light bg-white border border-secondary border-opacity-25 rounded-circle text-primary shadow-sm hover-elevate me-2" 
                                    data-bs-toggle="modal" 
                                    style="width: 40px; height: 40px;"
                                    data-bs-target="#editEmployeeModal{{ $emp->id }}"
                                    title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Bouton Suppression -->
                            <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-light bg-white border border-secondary border-opacity-25 rounded-circle text-danger shadow-sm hover-elevate delete-btn" 
                                        style="width: 40px; height: 40px;"
                                        title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Modification Employé -->
                    <div class="modal fade" id="editEmployeeModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header border-0 p-4 pb-0">
                                    <h5 class="fw-bold">Modifier Employé</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('employees.update', $emp->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">NOM</label>
                                                <input type="text" name="nom" class="form-control rounded-3" value="{{ $emp->nom }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">PRÉNOM</label>
                                                <input type="text" name="prenom" class="form-control rounded-3" value="{{ $emp->prenom }}" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">EMAIL</label>
                                                <input type="email" name="email" class="form-control rounded-3" value="{{ $emp->email }}" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">TÉLÉPHONE</label>
                                                <input type="text" name="telephone" class="form-control rounded-3" value="{{ $emp->telephone }}" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">MOT DE PASSE <span class="text-muted fw-normal">(Laisser vide si inchangé)</span></label>
                                                <input type="password" name="password" class="form-control rounded-3">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">RÔLE</label>
                                                <select name="role" class="form-select rounded-3" required>
                                                    <option value="receptionniste" {{ $emp->role == 'receptionniste' ? 'selected' : '' }}>Réceptionniste</option>
                                                    <option value="technicien" {{ $emp->role == 'technicien' ? 'selected' : '' }}>Technicien</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajout Employé -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold">Nouvel Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NOM</label>
                            <input type="text" name="nom" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">PRÉNOM</label>
                            <input type="text" name="prenom" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">EMAIL</label>
                            <input type="email" name="email" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">TÉLÉPHONE</label>
                            <input type="text" name="telephone" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">MOT DE PASSE</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">RÔLE</label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="receptionniste">Réceptionniste</option>
                                <option value="technicien">Technicien</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Créer le compte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: #e0e7ff; }
    .bg-soft-info { background-color: #e0f2fe; }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
        transition: background-color 0.2s ease;
    }
    
    .hover-elevate {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-elevate:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<!-- Scripts pour AJAX et SweetAlert -->
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Statut AJAX
        const toggleBtns = document.querySelectorAll('.toggle-status-btn');
        
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const btnElement = this;
                
                // Sauvegarder l'état original pour le spinner
                const originalText = btnElement.innerText;
                btnElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                btnElement.disabled = true;

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Mise à jour de l'UI
                        if(data.actif) {
                            btnElement.classList.remove('bg-danger', 'text-danger', 'border-danger');
                            btnElement.classList.add('bg-success', 'text-success', 'border-success');
                            btnElement.innerText = 'Actif';
                        } else {
                            btnElement.classList.remove('bg-success', 'text-success', 'border-success');
                            btnElement.classList.add('bg-danger', 'text-danger', 'border-danger');
                            btnElement.innerText = 'Inactif';
                        }
                        
                        // Petite animation de succès douce
                        btnElement.style.transform = 'scale(1.1)';
                        setTimeout(() => btnElement.style.transform = 'scale(1)', 200);
                        
                        // Notification toast (SweetAlert2)
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnElement.innerText = originalText;
                    Swal.fire('Erreur', 'Une erreur est survenue lors de la mise à jour du statut.', 'error');
                })
                .finally(() => {
                    btnElement.disabled = false;
                });
            });
        });

        // Confirmation de suppression avec SweetAlert2
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Cette action est irréversible !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler',
                    background: '#fff',
                    borderRadius: '1rem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
