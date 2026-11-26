@props([
    'subtitle' => 'Wedding Organizer',
])

@if (session('login_curtain'))
    <style>
        .md-login-curtain {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #5C1620;
            color: #fff;
            animation: md-intro-out 0.9s cubic-bezier(0.77, 0, 0.18, 1) 1.15s forwards;
        }
        .md-login-curtain__inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.9rem;
            text-align: center;
            padding: 1.5rem;
        }
        .md-login-curtain__logo {
            font-family: "Fraunces", ui-serif, Georgia, serif;
            font-size: clamp(2.4rem, 8vw, 4rem);
            font-weight: 400;
            line-height: 1.1;
            letter-spacing: 0.02em;
            color: #fff;
            animation: md-intro-text 0.9s ease 0.08s both;
        }
        .md-login-curtain__line {
            display: block;
            height: 1px;
            width: 0;
            background: #B08D57;
            animation: md-intro-line 0.85s ease 0.28s forwards;
        }
        .md-login-curtain__sub {
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 400;
            line-height: 1.4;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            animation: md-intro-text 0.9s ease 0.42s both;
        }
        @keyframes md-intro-text {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }
        @keyframes md-intro-line {
            to { width: 9rem; }
        }
        @keyframes md-intro-out {
            to { transform: translateY(-100%); visibility: hidden; }
        }
        @media (prefers-reduced-motion: reduce) {
            .md-login-curtain { display: none !important; }
        }
    </style>

    <div class="md-login-curtain" aria-hidden="true" onanimationend="if (event.target === this) this.remove()">
        <div class="md-login-curtain__inner">
            <span class="md-login-curtain__logo">MyDream</span>
            <span class="md-login-curtain__line"></span>
            <span class="md-login-curtain__sub">{{ $subtitle }}</span>
        </div>
    </div>
@endif
