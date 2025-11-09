<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registratsiya | Online Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-tr from-pink-500 via-red-500 to-yellow-400 text-white min-h-screen flex items-center justify-center p-5 relative overflow-hidden">

    <!-- Fon naqsh -->
    <div class="absolute inset-0 opacity-20 bg-[url('https://www.toptal.com/designers/subtlepatterns/patterns/memphis-mini.png')]"></div>

    <!-- Yorug‘lik effekti -->
    <div id="shadow" class="fixed top-0 left-0 z-0 h-32 w-32 rounded-full bg-white blur-[80px] transition-opacity duration-300 opacity-0"></div>

    <!-- Karta (Form) -->
    <div id="card" class="relative z-10 w-full max-w-md bg-white/20 backdrop-blur-2xl p-8 rounded-3xl border border-white/30 shadow-2xl">

        <!-- Sarlavha -->
        <div class="mb-6 inline-flex items-center rounded-full border border-white/10 bg-white/20 p-1">
            <div class="px-6 py-2 rounded-full bg-white/30 text-sm font-medium text-pink-900">
                {{ __('Registratsiya') }}
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-6 text-white">Create your account</h2>

        <!-- FORM -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name"
                    class="mt-1 w-full rounded-md border border-white/30 bg-white/30 text-pink-900 placeholder-pink-700/50 p-3 focus:outline-none focus:ring-2 focus:ring-white/50"
                    type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-yellow-200" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email"
                    class="mt-1 w-full rounded-md border border-white/30 bg-white/30 text-pink-900 placeholder-pink-700/50 p-3 focus:outline-none focus:ring-2 focus:ring-white/50"
                    type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-yellow-200" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password"
                    class="mt-1 w-full rounded-md border border-white/30 bg-white/30 text-pink-900 placeholder-pink-700/50 p-3 focus:outline-none focus:ring-2 focus:ring-white/50"
                    type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-yellow-200" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation"
                    class="mt-1 w-full rounded-md border border-white/30 bg-white/30 text-pink-900 placeholder-pink-700/50 p-3 focus:outline-none focus:ring-2 focus:ring-white/50"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-yellow-200" />
            </div>

            <!-- Pastki tugmalar -->
            <div class="flex items-center justify-between">
                <a class="text-sm text-white/80 hover:text-white transition" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ml-3 px-5 py-2 bg-white text-pink-600 font-bold rounded-md shadow-md hover:bg-pink-100 transition">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- JS Yorug‘lik effekti -->
    <script>
        const shadow = document.getElementById("shadow");
        const card = document.getElementById("card");

        document.body.addEventListener("mousemove", (e) => {
            const { clientX, clientY } = e;
            if (e.target.closest("#card")) {
                shadow.style.transform = `translate(${clientX - 60}px, ${clientY - 60}px)`;
                shadow.style.opacity = "0.6";
            } else {
                shadow.style.opacity = "0";
            }
        });
    </script>
</body>
</html>
