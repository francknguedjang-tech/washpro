<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ asset('vendor/font-inter/index.css') }}" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #2ecc71;
            --danger-color: #ef476f;
            --warning-color: #ffd166;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #eef2ff; /* Premium light blue */
            --sidebar-width: 280px;
            --sidebar-bg: #111827; /* Dark black/slate */
            --sidebar-text: #9ca3af;
            --sidebar-hover: #1f2937;
            --sidebar-active-bg: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --topbar-bg: #ffffff; /* Clean white top bar for contrast */
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.3);
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.01);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.25rem;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            padding: 0.5rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-category {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6b7280;
            margin: 1.5rem 1rem 0.75rem;
            font-weight: 700;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-link svg, .nav-link i {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            transition: all 0.2s ease;
            font-size: 1.1rem;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }

        .nav-link:hover svg, .nav-link:hover i {
            transform: translateX(3px);
            color: #ffffff;
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
            font-weight: 600;
        }

        .nav-link.active svg, .nav-link.active i {
            color: white;
        }

        .nav-link.logout {
            color: #ef4444; /* red 500 */
            margin-top: auto;
            border: 1px solid transparent;
        }

        .nav-link.logout:hover {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
        }
        
        .nav-link.logout:hover i {
            color: #f87171;
        }

        /* User Profile in Sidebar Bottom */
        .user-profile {
            padding: 1.25rem;
            margin: 1rem;
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .user-info h6 { margin: 0; font-size: 0.9rem; font-weight: 700; color: #ffffff; }
        .user-info small { color: #9ca3af; font-size: 0.75rem; text-transform: uppercase; font-weight: 600; }

        /* Main Content Styling */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 2.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: calc(100vw - var(--sidebar-width));
        }

        .navbar-mobile {
            display: none;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); width: 280px; box-shadow: 20px 0 25px -5px rgba(0, 0, 0, 0.1); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem; max-width: 100vw; }
            .navbar-mobile { display: flex; }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(4px);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .sidebar-overlay.show { display: block; opacity: 1; }
        }

        /* Top Bar Styling */
        .top-bar {
            background: var(--topbar-bg);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); /* Soft shadow */
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 800;
            margin: -2.5rem -2.5rem 2.5rem -2.5rem; /* Negate main-content padding */
        }
        
        @media (max-width: 991px) {
            .top-bar {
                margin: -1.5rem -1.5rem 1.5rem -1.5rem;   
            }
        }

        .notification-btn {
            position: relative;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: var(--text-dark);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-btn:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 0.25rem 0.4rem;
            font-size: 0.6rem;
            transform: translate(25%, -25%);
        }

        .dropdown-notifications {
            width: 350px;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            padding: 0;
            overflow: hidden;
        }

        .notification-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
            cursor: pointer;
            display: flex;
            gap: 12px;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item.unread {
            background-color: #f0f7ff;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            background: #e0e7ff;
            color: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Navbar -->
    <div class="navbar-mobile">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light border-0" id="sidebarToggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>
            <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">
        </div>
        <div class="avatar bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.8rem;">
           <div class="avatar bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
    @if(Auth::check())
        {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
    @else
        <i class="fa fa-user"></i>
    @endif
</div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;">
        </div>

        <nav class="sidebar-menu">
            <div class="menu-category">MENU PRINCIPAL</div>
            <a href="{{ route('receptionniste.dashboard') }}" class="nav-link {{ request()->routeIs('receptionniste.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill me-2"></i>
                Tableau de bord
            </a>

            <div class="menu-category">GESTION</div>
            <a href="{{ route('depots.create') }}" class="nav-link {{ request()->routeIs('depots.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau Dépôt
            </a>
            <a href="{{ route('depots.index') }}" class="nav-link {{ request()->routeIs('depots.index') || request()->routeIs('depots.show') || request()->routeIs('depots.edit') ? 'active' : '' }}">
                <i class="bi bi-list-ul me-2"></i>
                Historique des Dépôts
            </a>
            <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>
                Historique Clients
            </a>
            <a href="{{ route('paiements.index') }}" class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2 me-2"></i>
                Historique Paiements
            </a>

            <div class="mt-auto pt-4">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Déconnexion
                </a>
            </div>
        </nav>

        <div class="user-profile">
            <div class="avatar">
                {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
            </div>
            <div class="user-info">
                <h6>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</h6>
                <small>{{ Auth::user()->role }}</small>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="notification-btn" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <span id="notifBadge" class="badge rounded-pill bg-danger notification-badge d-none">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-notifications" aria-labelledby="notifDropdown">
                        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            <button class="btn btn-link btn-sm text-decoration-none p-0" id="markAllRead" style="font-size: 0.75rem;">Tout marquer comme lu</button>
                        </div>
                        <div id="notifList" class="overflow-auto" style="max-height: 400px;">
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bell-slash fs-2 mb-2 d-block opacity-25"></i>
                                <small>Aucune nouvelle notification</small>
                            </div>
                        </div>
                        <div class="p-2 border-top text-center bg-light">
                            <a href="#" class="small text-primary text-decoration-none fw-bold">Voir tout l'historique</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="dropdown ms-3 border-start ps-3">
                    <button class="btn border-0 p-0 text-dark fw-bold d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold shadow-sm" style="width: 42px; height: 42px; font-size: 1.1rem;">
                            {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
                        </div>
                        <div class="d-none d-sm-flex flex-column align-items-start text-start ms-1">
                            <span class="fw-bold text-dark lh-1" style="font-size: 0.95rem;">
                                {{ Auth::user()->prenom }} <span class="badge bg-light text-dark border ms-1" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->role ?? 'STANDARD') }}</span>
                            </span>
                            <span class="text-muted lh-1 mt-1" style="font-size: 0.8rem;">Compte {{ ucfirst(Auth::user()->role ?? 'Actif') }}</span>
                        </div>
                        <i class="bi bi-chevron-down small text-muted ms-2 mt-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-3 p-2" style="min-width: 220px;">
                        <li>
                            <div class="px-3 py-2">
                                <span class="fw-bold d-block text-dark">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                                <small class="text-muted">{{ Auth::user()->telephone ?? Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @if(Auth::user()->role === 'client')
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.settings') }}">
                                <i class="bi bi-gear text-secondary"></i> Paramètres
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.help') }}">
                                <i class="bi bi-question-circle text-primary"></i> Aide
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @else
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium text-muted" href="#">
                                <i class="bi bi-gear"></i> Paramètres
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium text-muted" href="#">
                                <i class="bi bi-question-circle"></i> Aide
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item rounded-3 text-danger fw-semibold d-flex align-items-center gap-2 py-2" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    <!-- SweetAlert2 Local -->
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Bootstrap Bundle JS Local -->
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // Scripts pour le responsive sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggle = document.getElementById('sidebarToggle');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            }

            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            // Notification System
            function fetchNotifications() {
                fetch('{{ route("notifications.index") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateBadge(data.unreadCount);
                        renderNotifications(data.notifications);
                    });
            }

            function updateBadge(count) {
                const badge = document.getElementById('notifBadge');
                if (count > 0) {
                    badge.innerText = count > 9 ? '9+' : count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }

            function renderNotifications(notifs) {
                const list = document.getElementById('notifList');
                if (notifs.length === 0) return;

                let html = '';
                notifs.forEach(n => {
                    const date = new Date(n.created_at || n.date_envoi);
                    const time = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    
                    html += `
                        <div class="notification-item ${n.lu ? '' : 'unread'}" onclick="markAsRead(${n.id})">
                            <div class="notification-icon">
                                <i class="bi ${n.message.includes('prêt') ? 'bi-check2-circle' : 'bi-info-circle'}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-semibold text-dark mb-1">${n.message}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-1"></i> ${time}
                                </div>
                            </div>
                            ${!n.lu ? '<div class="ms-2"><div class="bg-primary rounded-circle" style="width: 6px; height: 6px;"></div></div>' : ''}
                        </div>
                    `;
                });
                list.innerHTML = html;
            }

            window.markAsRead = function(id) {
                fetch(`/api/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => fetchNotifications());
            };

            document.getElementById('markAllRead')?.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('{{ route("notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => fetchNotifications());
            });

            // Refresh notifications every 30 seconds
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        });
    </script>
    <!-- SweetAlert2 Global Notifications & Confirmations -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration Toasts
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Affichage des messages flash de session
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{!! session('success') !!}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{!! session('error') !!}"
                });
            @endif

            // Interception globale des confirmations (formulaires et boutons avec onsubmit/onclick = "return confirm(...)")
            // Pour les formulaires
            document.querySelectorAll('form').forEach(form => {
                const submitAttr = form.getAttribute('onsubmit');
                if (submitAttr && submitAttr.includes('confirm(')) {
                    const match = submitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) {
                        form.removeAttribute('onsubmit');
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Confirmation requise',
                                text: match[1],
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Oui, continuer',
                                cancelButtonText: 'Annuler',
                                reverseButtons: true,
                                customClass: { popup: 'rounded-4' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        });
                    }
                }
            });

            // Pour les boutons ou liens (si utilisés)
            document.querySelectorAll('[onclick*="confirm("]').forEach(el => {
                const clickAttr = el.getAttribute('onclick');
                if (clickAttr) {
                    const match = clickAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) {
                        el.removeAttribute('onclick');
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Êtes-vous sûr ?',
                                text: match[1],
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Oui, confirmer',
                                cancelButtonText: 'Annuler',
                                reverseButtons: true,
                                customClass: { popup: 'rounded-4' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    if (el.tagName === 'A' && el.href) {
                                        window.location.href = el.href;
                                    } else if (el.tagName === 'BUTTON' && el.type === 'submit' && el.form) {
                                        el.form.submit();
                                    }
                                }
                            });
                        });
                    }
                }
            });
        });

        // Mobile Sidebar Toggle Script
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if(sidebarToggle && sidebar && overlay) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>