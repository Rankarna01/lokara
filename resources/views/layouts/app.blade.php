<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lokara POS - Super Admin Platform' }}</title>

    <!-- Progressive Web App (PWA) Meta & Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f97316">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Lokara POS">
    <link rel="apple-touch-icon" href="/images/logo-lokarapos.png">
    <link rel="icon" type="image/png" href="/images/logo-lokarapos.png">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 (FA Free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        surface: '#F8FAFC',
                        orange: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                        },
                        dark: '#111827',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    borderRadius: {
                        'card': '8px',
                    }
                }
            }
        }
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            color: #111827;
            -webkit-font-smoothing: antialiased;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>

    @livewireStyles
</head>
<body class="bg-surface text-dark min-h-screen antialiased flex flex-col">

    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts
    @stack('scripts')

    <!-- PWA Service Worker Registration & Install Manager -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('PWA ServiceWorker registered with scope: ', registration.scope);
                }, function(err) {
                    console.log('PWA ServiceWorker registration failed: ', err);
                });
            });
        }

        // Global PWA Prompt Listener
        let deferredPwaPrompt = null;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPwaPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-prompt-ready'));
        });

        window.addEventListener('appinstalled', function() {
            deferredPwaPrompt = null;
            localStorage.setItem('lokara_pwa_installed', 'true');
            window.dispatchEvent(new CustomEvent('pwa-installed'));
        });

        function pwaInstallManager() {
            return {
                canInstall: false,
                showIosModal: false,
                isIos: false,
                init: function() {
                    var isStandalone = window.matchMedia('(display-mode: standalone)').matches 
                        || window.navigator.standalone === true 
                        || document.referrer.includes('android-app://');

                    if (isStandalone || localStorage.getItem('lokara_pwa_installed') === 'true') {
                        this.canInstall = false;
                        return;
                    }

                    var ua = (window.navigator.userAgent || '').toLowerCase();
                    this.isIos = /iphone|ipad|ipod/.test(ua);
                    this.canInstall = true;

                    var self = this;
                    window.addEventListener('pwa-prompt-ready', function() {
                        if (!isStandalone && localStorage.getItem('lokara_pwa_installed') !== 'true') {
                            self.canInstall = true;
                        }
                    });

                    window.addEventListener('pwa-installed', function() {
                        self.canInstall = false;
                    });
                },
                promptInstall: function() {
                    var self = this;
                    if (deferredPwaPrompt) {
                        deferredPwaPrompt.prompt();
                        deferredPwaPrompt.userChoice.then(function(choice) {
                            if (choice && choice.outcome === 'accepted') {
                                self.canInstall = false;
                                localStorage.setItem('lokara_pwa_installed', 'true');
                            }
                            deferredPwaPrompt = null;
                        });
                    } else {
                        this.showIosModal = true;
                    }
                },
                markAsInstalled: function() {
                    this.canInstall = false;
                    this.showIosModal = false;
                    localStorage.setItem('lokara_pwa_installed', 'true');
                }
            };
        }
    </script>
</body>
</html>

