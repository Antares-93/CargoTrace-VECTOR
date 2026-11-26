<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In • CargoTrace Vector</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #E2E8F0;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-slate-100">

    <!-- Center Card Container -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 p-3.5 sm:p-5 border border-slate-200">
        
        <!-- Left Side: Cargo Imagery & Multimodal Telemetry Card -->
        <div class="rounded-2xl p-6 sm:p-8 flex flex-col justify-between text-white relative overflow-hidden min-h-[460px] md:min-h-[580px] shadow-inner group">
            <!-- Background Image: Giant Container Vessel with Deep Dark Overlay -->
            <img src="/images/hero-cargo-ship.jpg" alt="Ocean Cargo Ship" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/70 to-emerald-950/80"></div>

            <!-- Vector Logo Top Left -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-10 h-10 object-contain brightness-0 invert drop-shadow">
                <div>
                    <span class="font-black text-xl tracking-wider">VECTOR</span>
                    <span class="text-[10px] block font-mono text-emerald-300 tracking-widest uppercase">CargoTrace</span>
                </div>
            </div>

            <!-- Center Showcase: Dual Air Express & Ocean Fleet Telemetry Cards -->
            <div class="relative z-10 my-auto py-6 space-y-3">
                <!-- Air Express Telemetry Card -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-3 rounded-2xl flex items-center gap-3.5 shadow-xl hover:bg-white/15 transition">
                    <img src="/images/airplane_sky.jpg" alt="Air Cargo Plane" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-white/30 shadow-sm">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            <span class="text-[9px] font-mono uppercase tracking-wider text-cyan-300 font-bold">Air Express Fleet</span>
                        </div>
                        <span class="text-xs font-extrabold text-white truncate block">Boeing 777F • Flight QR-803F</span>
                        <span class="text-[10px] text-slate-300 font-mono">485 Kts • Trans-Eurasian Skyway</span>
                    </div>
                </div>

                <!-- Ocean Lead Vessel Card -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-3 rounded-2xl flex items-center gap-3.5 shadow-xl hover:bg-white/15 transition">
                    <img src="/images/hero-ship.jpg" alt="Ocean Container Vessel" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-white/30 shadow-sm">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="text-[9px] font-mono uppercase tracking-wider text-emerald-300 font-bold">Ocean Freight Lead</span>
                        </div>
                        <span class="text-xs font-extrabold text-white truncate block">Maersk Mc-Kinney • 18,270 TEU</span>
                        <span class="text-[10px] text-slate-300 font-mono">21.4 Kts • Malacca Strait Corridor</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Motivational Tagline -->
            <div class="relative z-10 space-y-1.5 mt-auto">
                <p class="text-[11px] font-medium text-emerald-300 uppercase tracking-widest font-mono">Global Multimodal Telemetry</p>
                <h2 class="text-xl sm:text-2xl font-extrabold leading-tight tracking-tight text-white">
                    Experience The Magic Of Global Cargo Visibility
                </h2>
                <p class="text-[11px] text-slate-300/90 pt-1 leading-relaxed">
                    Real-time container ship AIS and air express ADS-B flight telemetry in one unified operations platform.
                </p>
            </div>
        </div>

        <!-- Right Side: Clean Login Form -->
        <div class="p-6 sm:p-10 flex flex-col justify-center">
            
            <!-- Mini Logo (Simple, monochrome) -->
            <div class="mb-4">
                <img src="/images/vector-logo-icon.png" alt="Vector" class="w-8 h-8 object-contain brightness-0 opacity-90">
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Sign in to your account</h1>
            <p class="text-xs text-slate-500 mt-1.5 mb-6">
                Access your consignments, radar GIS map, and telemetry in one place.
            </p>

            @if (session('error'))
                <div class="bg-red-50 text-red-600 border border-red-200 text-xs px-3.5 py-2.5 rounded-xl mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 border border-red-200 text-xs px-3.5 py-2.5 rounded-xl mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@cargotrace.com') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition placeholder:text-slate-400"
                           placeholder="name@company.com">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                    </div>
                    <div class="relative">
                        <input id="login-password" type="password" name="password" value="password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition pr-10"
                               placeholder="••••••••">
                        <button type="button" onclick="const p = document.getElementById('login-password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                    <span>Sign In</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- Footer link -->
            <div class="text-center mt-6 text-xs text-slate-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:underline">Sign up</a>
            </div>

        </div>

    </div>

</body>
</html>
