<header>
    <div class="wrap nav">
        <a href="{{ url('/') }}" class="logo-link">
            <img class="logo" src="{{ asset('assets/frontend/v1/images/header-logo.png') }}" alt="BRICS Artist Registry">
        </a>
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mainNav">
            <span></span><span></span><span></span>
        </button>
        <nav id="mainNav" aria-label="Main">
            @foreach (getMenu()->where('is_main', 1) as $menu)
                @if ($menu->childrenRecursive->isEmpty())
                    <a href="{{ url($menu->url) }}">{{ $menu->title }}</a>
                @else
                    <div class="nav-dropdown" id="menuDropdown-{{ $menu->id }}">
                        <button type="button" class="dropdown-toggle" id="menuToggle-{{ $menu->id }}" aria-expanded="false" aria-haspopup="true">{{ $menu->title }} <span class="menu-chevron"></span> </button>
	

                        <ul class="dropdown-menu" role="menu">
                            @foreach ($menu->childrenRecursive as $child)
                                @include('frontend.layouts.partials.menu-item', ['menu' => $child])
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
            @auth
                <div class="nav-dropdown">
                    <button type="button" class="dropdown-toggle" id="accountMenu" aria-expanded="false" aria-haspopup="true">My Account <span class="menu-chevron"></span> </button>
                    <ul class="dropdown-menu" role="menu">
                        <li role="none"><a role="menuitem" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li role="none"><a role="menuitem" href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                </div>
            @endauth
        </nav>
        @guest
            <div class="auth-nav-actions">
                <a href="{{ route('login') }}" class="login">Login</a>
                <a href="{{ url('artist-registration') }}" class="register">Register</a>
                
            </div>
        @endguest
    </div>
</header>
<div class="nav-backdrop" id="navBackdrop"></div>

@push('scripts')
<script>
(function () {
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');
    var backdrop = document.getElementById('navBackdrop');
    var mobileQuery = window.matchMedia('(max-width: 1099.98px)');

    function closeSubmenus(except) {
        if (!nav) return;
        nav.querySelectorAll('.nav-dropdown.is-open').forEach(function (item) {
            if (item === except) return;
            item.classList.remove('is-open');
            var btn = item.querySelector('.dropdown-toggle');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    function closeMobile() {
        if (toggle) {
            toggle.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
        }
        if (nav) nav.classList.remove('is-open');
        if (backdrop) backdrop.classList.remove('is-open');
        closeSubmenus(null);
        document.body.style.overflow = '';
    }

    function openMobile() {
        if (toggle) {
            toggle.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Close menu');
        }
        if (nav) nav.classList.add('is-open');
        if (backdrop) backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    if (toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (nav && nav.classList.contains('is-open')) closeMobile();
            else openMobile();
        });
    }

    if (backdrop) backdrop.addEventListener('click', closeMobile);

    // Mobile / tablet: tap a parent menu to expand or collapse its submenu.
    // Capture phase so this is the only handler that acts on the toggle in the drawer.
    document.addEventListener('click', function (e) {
        if (!nav || !mobileQuery.matches) return;
        var btn = e.target.closest ? e.target.closest('.nav-dropdown > .dropdown-toggle') : null;
        if (!btn || !nav.contains(btn)) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        var item = btn.parentElement;
        var willOpen = !item.classList.contains('is-open');
        closeSubmenus(willOpen ? item : null);
        item.classList.toggle('is-open', willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    }, true);

    // Following any link inside the drawer closes it.
    if (nav) {
        nav.addEventListener('click', function (e) {
            if (mobileQuery.matches && e.target.closest && e.target.closest('a')) closeMobile();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMobile();
    });

    window.addEventListener('resize', function () {
        if (!mobileQuery.matches) closeMobile();
    });
})();
</script>
@endpush
