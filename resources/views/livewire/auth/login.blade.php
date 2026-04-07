<div class="animate-slide-up w-full max-w-md mx-auto px-4" style="position:relative;z-index:10;">
    <div class="glass-card p-8 md:p-10">

        {{-- Logo & Heading --}}
        <div class="flex flex-col items-center mb-8">
            <div style="
                width:56px;height:56px;
                background:linear-gradient(135deg,#6366f1,#8b5cf6);
                border-radius:16px;
                display:flex;align-items:center;justify-content:center;
                box-shadow:0 8px 20px rgba(99,102,241,0.35);
                margin-bottom:20px;
            ">
                <svg style="width:28px;height:28px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <h1 style="font-size:22px;font-weight:800;color:#111827;letter-spacing:-0.3px;margin-bottom:4px;">
                SMA Nusantara
            </h1>
            <p style="font-size:13px;color:#6b7280;font-weight:500;">
                Masuk ke Panel Admin
            </p>
        </div>

        {{-- Error global --}}
        @if (session('error'))
            <div style="
                            background:rgba(239,68,68,0.08);
                            border:1px solid rgba(239,68,68,0.25);
                            border-radius:12px;
                            padding:10px 14px;
                            margin-bottom:20px;
                            display:flex;align-items:center;gap:8px;
                        ">
                <svg style="width:16px;height:16px;color:#ef4444;flex-shrink:0;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <path stroke-linecap="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                </svg>
                <span style="font-size:13px;color:#dc2626;">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit="authenticate" style="display:flex;flex-direction:column;gap:18px;">

            {{-- Email --}}
            <div>
                <label for="email"
                    style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Email
                </label>
                <div style="position:relative;">
                    <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <svg style="width:16px;height:16px;color:#9ca3af;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input id="email" type="email" wire:model="email" placeholder="admin@sekolah.com"
                        autocomplete="email" style="
                            width:100%;
                            padding:11px 14px 11px 40px;
                            background:rgba(255,255,255,0.7);
                            border:1px solid {{ $errors->has('email') ? 'rgba(239,68,68,0.5)' : 'rgba(209,213,219,0.8)' }};
                            border-radius:12px;
                            font-size:14px;
                            color:#111827;
                            outline:none;
                            font-family:'Plus Jakarta Sans',sans-serif;
                            transition:border 0.2s,box-shadow 0.2s;
                        "
                        onfocus="this.style.borderColor='rgba(99,102,241,0.6)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)';"
                        onblur="this.style.borderColor='{{ $errors->has('email') ? 'rgba(239,68,68,0.5)' : 'rgba(209,213,219,0.8)' }}';this.style.boxShadow='none';" />
                </div>
                @error('email')
                    <p style="margin-top:5px;font-size:12px;color:#ef4444;display:flex;align-items:center;gap:4px;">
                        <svg style="width:12px;height:12px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <label for="password" style="font-size:13px;font-weight:600;color:#374151;">
                        Password
                    </label>
                </div>
                <div style="position:relative;">
                    <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <svg style="width:16px;height:16px;color:#9ca3af;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" type="password" wire:model="password" placeholder="••••••••"
                        autocomplete="current-password" x-data x-ref="pwdInput" style="
                            width:100%;
                            padding:11px 44px 11px 40px;
                            background:rgba(255,255,255,0.7);
                            border:1px solid {{ $errors->has('password') ? 'rgba(239,68,68,0.5)' : 'rgba(209,213,219,0.8)' }};
                            border-radius:12px;
                            font-size:14px;
                            color:#111827;
                            outline:none;
                            font-family:'Plus Jakarta Sans',sans-serif;
                            transition:border 0.2s,box-shadow 0.2s;
                        "
                        onfocus="this.style.borderColor='rgba(99,102,241,0.6)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)';"
                        onblur="this.style.borderColor='{{ $errors->has('password') ? 'rgba(239,68,68,0.5)' : 'rgba(209,213,219,0.8)' }}';this.style.boxShadow='none';" />
                    {{-- Toggle show/hide password --}}
                    <button type="button" onclick="
                            const inp = this.previousElementSibling;
                            const isText = inp.type === 'text';
                            inp.type = isText ? 'password' : 'text';
                            this.querySelector('.eye-open').style.display = isText ? 'block' : 'none';
                            this.querySelector('.eye-closed').style.display = isText ? 'none' : 'block';
                        "
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;">
                        <svg class="eye-open" style="width:16px;height:16px;display:block;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-closed" style="width:16px;height:16px;display:none;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p style="margin-top:5px;font-size:12px;color:#ef4444;display:flex;align-items:center;gap:4px;">
                        <svg style="width:12px;height:12px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div style="display:flex;align-items:center;gap:8px;">
                <input id="remember" type="checkbox" wire:model="remember" style="
                        width:16px;height:16px;
                        accent-color:#6366f1;
                        border-radius:4px;
                        cursor:pointer;
                    " />
                <label for="remember" style="font-size:13px;color:#6b7280;cursor:pointer;user-select:none;">
                    Ingat saya
                </label>
            </div>

            {{-- Submit button --}}
            <button type="submit" id="btn-login" wire:loading.attr="disabled" wire:target="authenticate" style="
                    width:100%;
                    padding:12px;
                    background:linear-gradient(135deg,#6366f1,#8b5cf6);
                    color:white;
                    font-weight:700;
                    font-size:14px;
                    border:none;
                    border-radius:12px;
                    cursor:pointer;
                    letter-spacing:0.2px;
                    box-shadow:0 4px 16px rgba(99,102,241,0.35);
                    transition:opacity 0.2s,transform 0.1s,box-shadow 0.2s;
                    font-family:'Plus Jakarta Sans',sans-serif;
                    display:flex;align-items:center;justify-content:center;gap:8px;
                    margin-top:4px;
                "
                onmouseover="this.style.boxShadow='0 6px 24px rgba(99,102,241,0.5)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 16px rgba(99,102,241,0.35)';this.style.transform='none';"
                onmousedown="this.style.transform='scale(0.98)';" onmouseup="this.style.transform='none';">
                <span wire:loading.remove wire:target="authenticate">
                    <svg style="width:16px;height:16px;display:inline;margin-right:4px;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk
                </span>
                <span wire:loading.flex wire:target="authenticate" style="align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" opacity="0.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a8 8 0 018 8"
                            stroke="white" />
                    </svg>
                    Memproses...
                </span>
            </button>

        </form>

        {{-- Footer --}}
        <p style="text-align:center;margin-top:24px;font-size:12px;color:#9ca3af;">
            &copy; {{ date('Y') }} SMA Nusantara. Sistem Manajemen Sekolah.
        </p>
    </div>
</div>

<style>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>