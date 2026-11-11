<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account • CargoTrace Vector</title>
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
        
        <!-- Left Side: Gradient Banner Card (matching Image 2) -->
        <div class="rounded-2xl p-8 sm:p-10 flex flex-col justify-between text-white relative overflow-hidden min-h-[400px] md:min-h-[580px] shadow-inner"
             style="background: linear-gradient(135deg, #1E3A8A 0%, #166534 50%, #047857 100%);">
            
            <div class="absolute -top-16 -left-16 w-56 h-56 rounded-full bg-cyan-400 opacity-30 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 opacity-25 blur-3xl pointer-events-none"></div>

            <!-- Vector Emblem -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-10 h-10 object-contain brightness-0 invert drop-shadow">
                <div>
                    <span class="font-black text-xl tracking-wider">VECTOR</span>
                    <span class="text-[10px] block font-mono text-emerald-200 tracking-widest uppercase">CargoTrace</span>
                </div>
            </div>

            <!-- Tagline (Image 2) -->
            <div class="relative z-10 space-y-2 mt-auto">
                <p class="text-xs font-medium text-emerald-200 uppercase tracking-widest font-mono">You can easily</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight tracking-tight text-white">
                    Get access your personal hub for clarity and productivity
                </h2>
                <p class="text-xs text-slate-200/80 pt-2 leading-relaxed">
                    Multimodal freight corridor tracking across sea routes and air express lanes.
                </p>
            </div>
        </div>

        <!-- Right Side: Create Account Form (exact elements from Image 2) -->
        <div class="p-6 sm:p-10 flex flex-col justify-center">
            
            <div class="mb-4">
                <img src="/images/vector-logo-icon.png" alt="Vector" class="w-8 h-8 object-contain">
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Create an account</h1>
            <p class="text-xs text-slate-500 mt-1.5 mb-5">
                Access your tasks, notes, and projects anytime, anywhere — and keep everything flowing in one place.
            </p>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 border border-red-200 text-xs px-3.5 py-2.5 rounded-xl mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name / Organization</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition placeholder:text-slate-400"
                           placeholder="Faraz Haidet or PT Samudera">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition placeholder:text-slate-400"
                           placeholder="farazhaidet786@gmail.com">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Account Role</label>
                        <select name="role" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm bg-white">
                            <option value="customer" selected>Customer</option>
                            <option value="operator">Logistics Operator</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone (optional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition"
                               placeholder="+62 812 3456">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input id="reg-password" type="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition pr-10"
                               placeholder="••••••••••">
                        <button type="button" onclick="const p = document.getElementById('reg-password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-sm transition"
                           placeholder="••••••••••">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 mt-2">
                    <span>Get Started</span>
                </button>
            </form>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-[10px] text-slate-400 uppercase tracking-widest"><span class="bg-white px-2">or continue with</span></div>
            </div>

            <!-- Social Auth Pills (Image 2) -->
            <div class="grid grid-cols-3 gap-2">
                <button type="button" class="py-1.5 px-3 border border-slate-200 rounded-lg text-slate-600 text-xs font-bold hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                    <span>Bē</span>
                </button>
                <button type="button" class="py-1.5 px-3 border border-slate-200 rounded-lg text-slate-600 text-xs font-bold hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                    <span class="text-red-500 font-extrabold">G</span> Google
                </button>
                <button type="button" class="py-1.5 px-3 border border-slate-200 rounded-lg text-slate-600 text-xs font-bold hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                    <span class="text-blue-600 font-bold">f</span>
                </button>
            </div>

            <div class="text-center mt-4 text-xs text-slate-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Sign In</a>
            </div>

        </div>

    </div>

</body>
</html>
