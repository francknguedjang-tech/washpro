{{-- Nom : admin/reports/index.blade.php | Rôle : Centre de sélection des rapports statistiques (Journalier, Mensuel, Services) --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Tableau des Statistiques</h2>
            <p class="text-muted mb-0 small">Consultez l'ensemble des rapports d'activité de votre pressing</p>
        </div>
    </div>

    {{-- Grille des différents types de rapports disponibles --}}
    <div class="row g-4">
        <!-- Rapport Journalier -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('reports.daily') }}" class="text-decoration-none h-100 d-block">
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-hover border-bottom border-4 border-primary">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-calendar-day fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Rapport Journalier</h5>
                        <p class="text-muted small mb-0">Consultez les dépôts, articles et recettes générés aujourd'hui.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rapport Mensuel -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('reports.monthly') }}" class="text-decoration-none h-100 d-block">
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-hover border-bottom border-4 border-success">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-calendar-month fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Rapport Mensuel</h5>
                        <p class="text-muted small mb-0">Statistiques mensuelles, recettes et moyenne par jour.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rapport par Service -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('reports.service') }}" class="text-decoration-none h-100 d-block">
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-hover border-bottom border-4 border-info">
                    <div class="card-body p-4 text-center">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-tags fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Rapport par Service</h5>
                        <p class="text-muted small mb-0">Analysez la performance et la rentabilité de chaque service.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Revenu Annuel -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('reports.revenues') }}" class="text-decoration-none h-100 d-block">
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-hover border-bottom border-4 border-warning">
                    <div class="card-body p-4 text-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Revenu Annuel</h5>
                        <p class="text-muted small mb-0">Visualisez l'historique de tous vos encaissements et revenus.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
