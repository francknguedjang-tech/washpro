@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('clients.index') }}" class="btn btn-icon btn-light rounded-circle p-0 me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-dark mb-1">Modifier Client</h2>
                    <p class="text-muted small fw-600 mb-0">Mise à jour des informations de {{ $client->nom }} {{ $client->prenom }}</p>
                </div>
            </div>

            <div class="card glass-panel border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-700">PRÉNOM</label>
                            <input type="text" name="prenom" class="form-control rounded-3 py-2 px-3 bg-light border-0" value="{{ old('prenom', $client->prenom) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-700">NOM</label>
                            <input type="text" name="nom" class="form-control rounded-3 py-2 px-3 bg-light border-0" value="{{ old('nom', $client->nom) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-700">TÉLÉPHONE</label>
                            <input type="text" name="telephone" class="form-control rounded-3 py-2 px-3 bg-light border-0" value="{{ old('telephone', $client->telephone) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-700">EMAIL (OPTIONNEL)</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 px-3 bg-light border-0" value="{{ old('email', $client->email) }}">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-700 shadow-sm">
                                <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-light rounded-pill py-2 text-muted fw-600">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }
    .fw-800 { font-weight: 800; }
    .fw-700 { font-weight: 700; }
    .fw-600 { font-weight: 600; }
</style>
@endsection
