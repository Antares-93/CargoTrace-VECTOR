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

    <!-- Center Card Container (matching Image 2) -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 p-3.5 sm:p-5 border border-slate-200">
        
        <!-- Left Side: Ambient Gradient Card (Image 2 style) -->
        <div class="rounded-2xl p-8 sm:p-10 flex flex-col justify-between text-white relative overflow-hidden min-h-[380px] md:min-h-[540px] shadow-inner"
             style="background: linear-gradient(135deg, #1E3A8A 0%, #166534 50%, #047857 100%);">
            <!-- Ambient blurred lighting spots -->
            <div class="absolute -top-16 -left-16 w-56 h-56 rounded-full bg-cyan-400 opacity-30 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 opacity-25 blur-3xl pointer-events-none"></div>

            <!-- Vector Emblem Top Left -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-10 h-10 object-contain brightness-0 invert drop-shadow">
                <div>
                    <span class="font-black text-xl tracking-wider">VECTOR</span>
                    <span class="text-[10px] block font-mono text-emerald-200 tracking-widest uppercase">CargoTrace</span>
                </div>
            </div>

            <!-- Bottom Motivational Tagline (Image 2) -->
            <div class="relative z-10 space-y-2 mt-auto">
                <p class="text-xs font-medium text-emerald-200 uppercase tracking-widest font-mono">You can easily</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight tracking-tight text-white">
                    Get access your personal hub for clarity and productivity
                </h2>
                <p class="text-xs text-slate-200/80 pt-2 leading-relaxed">
                    Real-time global shipment visibility, AIS vessel &amp; ADS-B radar, and intelligent delay telemetry.
                </p>
            </div>
        </div>

        <!-- Right Side: Form (Image 2 style) -->
        <div class="p-6 sm:p-10 flex flex-col justify-center">
            
            <!-- Mini Logo -->
            <div class="mb-4">
                <img src="/images/vector-logo-icon.png" alt="Vector" class="w-8 h-8 object-contain">
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

                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                    <span>Sign In</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- Quick Demo 1-Click Access Pill Bar (Crucial for Reviewers/Dosen!) -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-2.5">
                    1-Click Demo Evaluation Roles
                </p>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <a href="{{ route('demo.login', 'admin') }}" class="p-2 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition font-bold block">
                        Apex Admin
                        <span class="block text-[9px] font-normal text-purple-600">Full Access</span>
                    </a>
                    <a href="{{ route('demo.login', 'operator') }}" class="p-2 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100 transition font-bold block">
                        Operator
                        <span class="block text-[9px] font-normal text-cyan-600">Events &amp; Desks</span>
                    </a>
                    <a href="{{ route('demo.login', 'customer') }}" class="p-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition font-bold block">
                        Customer
                        <span class="block text-[9px] font-normal text-emerald-600">My Shipments</span>
                    </a>
                </div>
            </div>

            <!-- Footer link -->
            <div class="text-center mt-6 text-xs text-slate-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">Sign up</a>
            </div>

        </div>

    </div>

</body>
</html>
