{{-- Nom : welcome.blade.php | Rôle : Page d'accueil publique (Landing Page) présentant les services WashPro --}}
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Washpro - Pressing Moderne</title>
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-plus-jakarta-sans/index.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS for layout/grid backing -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/animate/animate.min.css') }}"/>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #38bdf8;
            --accent: #f43f5e;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.2);
            --card-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-main);
            color: var(--dark);
            background-color: var(--light);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Utilities */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Navbar Glass */
        .navbar-glass {
            background: var(--glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            font-weight: 600;
            color: var(--dark-light) !important;
            margin: 0 0.5rem;
            position: relative;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-modern {
            padding: 0.75rem 1.8rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white !important;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.4);
        }

        .btn-outline-modern {
            background: rgba(255,255,255,0.9);
            color: var(--primary) !important;
            border: 2px solid transparent;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .btn-outline-modern:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        /* Hero Section */
        .hero {
            position: relative;
            padding: 180px 0 120px;
            background: linear-gradient(180deg, #e0f2fe 0%, #ffffff 100%);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -5%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.3) 0%, transparent 60%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(40px);
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
            z-index: 2;
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #475569;
            margin-bottom: 2.5rem;
            max-width: 600px;
            line-height: 1.6;
            z-index: 2;
            position: relative;
        }

        .hero-image-wrapper {
            position: relative;
            z-index: 2;
        }

        .hero-img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
            transform: perspective(1000px) rotateY(-8deg) translateY(-10px);
            transition: transform 0.5s ease;
        }

        .hero-img:hover {
            transform: perspective(1000px) rotateY(0deg) translateY(0);
        }

        .hero-floating-card {
            position: absolute;
            bottom: -20px;
            left: -30px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.2rem 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: float 4s ease-in-out infinite;
            border: 1px solid rgba(255,255,255,0.5);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Section Titles */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .badge-modern {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        /* Services Cards */
        .service-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .service-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37,99,235,0.05) 0%, transparent 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--card-shadow);
            border-color: rgba(37,99,235,0.1);
        }

        .service-card:hover::after {
            opacity: 1;
        }

        .service-img-container {
            width: 100%;
            height: 200px;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .service-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .service-card:hover .service-img-container img {
            transform: scale(1.05);
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .service-desc {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Works Section */
        .works-section {
            background: #ffffff;
            padding: 100px 0;
            position: relative;
        }

        .step-item {
            text-align: center;
            position: relative;
            padding: 2rem;
        }

        .step-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }

        .step-item:hover .step-img {
            transform: scale(1.1) rotate(5deg);
        }

        .step-number {
            position: absolute;
            top: 10px;
            right: 50%;
            transform: translateX(60px);
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(37,99,235,0.3);
            z-index: 2;
        }

        .step-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        /* Features */
        .features-section {
            padding: 100px 0;
            background: var(--dark);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .features-section::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, transparent 60%);
            border-radius: 50%;
            z-index: 0;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            font-weight: 500;
            color: #cbd5e1;
        }

        .feature-icon {
            color: var(--secondary);
            font-size: 1.5rem;
            margin-right: 1.2rem;
            background: rgba(56, 189, 248, 0.1);
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .app-mockup {
            max-width: 100%;
            border-radius: 30px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
            border: 8px solid #1e293b;
            position: relative;
            z-index: 2;
        }

        /* CTA */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: relative;
        }

        .cta-box {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 4rem;
            text-align: center;
            color: white;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }

        /* Footer */
        .footer {
            background: #020617;
            color: #94a3b8;
            padding: 80px 0 30px;
        }

        .footer-title {
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            display: block;
            margin-bottom: 0.8rem;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: white;
        }

        .social-btn {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .social-btn:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 3rem; }
            .section-title { font-size: 2.2rem; }
            .hero { padding: 130px 0 80px; }
            .cta-box { padding: 2rem; }
        }
    </style>
</head>
<body>

    {{-- Barre de navigation principale --}}
    <nav class="navbar navbar-expand-lg fixed-top navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 36px; width: auto;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list fs-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#services">Nos Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">Comment ça marche</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Application</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    @auth
                        <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('client.dashboard') }}" class="btn-modern btn-primary-modern text-decoration-none">Mon Espace</a>
                    @else
                        <a href="{{ route('login') }}" class="text-dark-light fw-bold text-decoration-none hover:text-primary">Se connecter</a>
                        <a href="{{ route('register') }}" class="btn-modern btn-primary-modern text-decoration-none">Créer un compte</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Section Héro : Message principal et appel à l'action --}}
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="animate__animated animate__fadeInUp">
                        <span class="badge-modern mb-3">Service Premium</span>
                        <h1 class="hero-title">Votre pressing <br><span class="text-gradient">moderne</span>, simple et rapide.</h1>
                        <p class="hero-subtitle">Suivez votre linge en temps réel, recevez des notifications et profitez d’un service de pressing professionnel directement depuis votre smartphone.</p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            @auth
                                <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('client.dashboard') }}" class="btn-modern btn-primary-modern text-decoration-none px-5 py-3 fs-5">Accéder à l'application</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-modern btn-primary-modern text-decoration-none px-4 py-3">Créer un compte</a>
                                <a href="{{ route('login') }}" class="btn-modern btn-outline-modern text-decoration-none px-4 py-3"><i class="bi bi-box-arrow-in-right me-2"></i>Se connecter</a>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-wrapper animate__animated animate__zoomIn">
                        <img src="{{ asset('doc_images/pressing.jpg') }}" alt="Pressing professionnel" class="hero-img">
                        <div class="hero-floating-card">
                            <i class="bi bi-shield-check text-primary fs-2"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Qualité Garantie</h6>
                                <small class="text-muted">Nettoyage exceptionnel</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Services : Présentation des offres de nettoyage --}}
    <section id="services" class="py-5 bg-light">
        <div class="container py-5">
            <div class="section-header">
                <span class="badge-modern">Expertise</span>
                <h2 class="section-title">Nos services</h2>
                <p class="text-muted mx-auto" style="max-width:600px; font-size:1.1rem;">Prenez soin de vos vêtements avec nos services de nettoyage adaptés à vos besoins. Qualité et efficacité garanties.</p>
            </div>

            <div class="row g-4">
                <!-- Lavage simple -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-img-container">
                            <img src="{{ asset('doc_images/20210105_12.webp') }}" alt="Machine à laver">
                        </div>
                        <h3 class="service-title">Lavage simple</h3>
                        <p class="service-desc">Nettoyage professionnel de vos vêtements quotidiens.</p>
                    </div>
                </div>
                <!-- Lavage + repassage -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-img-container">
                            <img src="{{ asset('doc_images/apprendre-repassage.jpeg') }}" alt="Repassage et lavage">
                        </div>
                        <h3 class="service-title">Lavage + repassage</h3>
                        <p class="service-desc">Lavage complet et repassage impeccable pour vos chemises et pantalons.</p>
                    </div>
                </div>
                <!-- Repassage -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-img-container">
                            <img src="{{ asset('doc_images/Planchar-ropa-delicada.jpg') }}" alt="Fer à repasser">
                        </div>
                        <h3 class="service-title">Repassage seul</h3>
                        <p class="service-desc">Vos vêtements parfaitement repassés avec soin et précision.</p>
                    </div>
                </div>
                <!-- Nettoyage de chaussures -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-img-container">
                            <img src="{{ asset('doc_images/OIP (2).jfif') }}" onerror="this.src='{{ asset('doc_images/OIP.jfif') }}'" alt="Chaussures nettoyées">
                        </div>
                        <h3 class="service-title">Nettoyage chaussures</h3>
                        <p class="service-desc">Nettoyage approfondi et entretien de vos chaussures préférées.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Fonctionnement : Les étapes du service --}}
    <section id="how-it-works" class="works-section">
        <div class="container">
            <div class="section-header">
                <span class="badge-modern">Simplicité</span>
                <h2 class="section-title">Comment fonctionne notre pressing ?</h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <img src="{{ asset('doc_images/sotto_sx.jpg') }}" alt="Dépôt linge" class="step-img">
                                <h4 class="step-title">Déposez votre linge</h4>
                                <p class="text-muted small">En boutique ou point relais</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <img src="{{ asset('doc_images/hemden-buegeln-900x600.jpg') }}" alt="Lavage" class="step-img">
                                <h4 class="step-title">Lavage & Repassage</h4>
                                <p class="text-muted small">Par nos experts</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <img src="{{ asset('doc_images/OIP (1).jfif') }}" alt="Notification" class="step-img">
                                <h4 class="step-title">Notification</h4>
                                <p class="text-muted small">Recevez une alerte sur mobile</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <img src="{{ asset('doc_images/istockphoto-643275906-170667a.jpg') }}" alt="Récupération" class="step-img">
                                <h4 class="step-title">Récupérez le linge</h4>
                                <p class="text-muted small">Propre et prêt à ranger</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fonctionnalités -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 z-2 position-relative">
                    <span class="badge-modern bg-white text-primary mb-3">Technologie</span>
                    <h2 class="section-title text-white mb-4">Pourquoi utiliser notre application ?</h2>
                    <ul class="feature-list mt-4">
                        <li class="feature-item">
                            <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                            <div>Suivi du linge en temps réel</div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon"><i class="bi bi-bell-fill"></i></div>
                            <div>Notification quand le linge est prêt</div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon"><i class="bi bi-journal-text"></i></div>
                            <div>Historique complet de vos dépôts</div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                            <div>Service rapide, sécurisé et transparent</div>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 text-center z-2 position-relative">
                    <img src="{{ asset('doc_images/-a108142.png') }}" alt="Mobile App Dashboard" class="app-mockup img-fluid" style="background:#fff;">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section" style="background: url('{{ asset('doc_images/pressing.jpg') }}') center/cover fixed;">
        <div style="background: rgba(15, 23, 42, 0.8); position: absolute; inset:0;"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="cta-box text-center">
                        <h2 class="display-5 fw-bold mb-4 text-white">Prêt à essayer notre pressing ?</h2>
                        <p class="lead mb-5 text-white-50">Confiez-nous votre linge aujourd'hui et redécouvrez le plaisir de vêtements toujours impeccables sans le moindre effort.</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('register') }}" class="btn-modern btn-primary-modern text-decoration-none px-5 py-3 fs-5 shadow-lg">Créer un compte</a>
                            <a href="#services" class="btn-modern btn-outline-modern text-decoration-none px-5 py-3 fs-5 bg-white text-dark">Voir nos services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5 border-bottom border-dark pb-5 mb-4">
                <div class="col-lg-4">
                    <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-4">
                    <p class="mb-4">Votre pressing moderne, simple et rapide au bout des doigts.</p>
                    <div>
                        <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">À propos</h5>
                    <a href="#" class="footer-link">Notre histoire</a>
                    <a href="#" class="footer-link">L'équipe</a>
                    <a href="#" class="footer-link">Carrières</a>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Informations</h5>
                    <p class="mb-2"><i class="bi bi-clock me-2"></i> Lundi - Samedi</p>
                    <p class="ms-4 text-white">08:00 - 19:00</p>
                    <p class="mb-2 mt-3"><i class="bi bi-clock me-2"></i> Dimanche</p>
                    <p class="ms-4 text-white">Fermé</p>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Contact & Adresse</h5>
                    <p class="mb-2"><i class="bi bi-geo-alt me-2"></i> Douala, Village Ndogpassi, Cameroun</p>
                    <p class="mb-2"><i class="bi bi-telephone me-2"></i> +237 698 25 57 25</p>
                    <p class="mb-0"><i class="bi bi-envelope me-2"></i> francknguedjang@gmail.com</p>
                </div>
            </div>
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Washpro. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
