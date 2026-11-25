<footer id="contact" class="site-footer">
    {{-- Inline SVG icon sprite (no icon-font dependency) --}}
    <svg class="ft-sprite" width="0" height="0" aria-hidden="true" focusable="false">
        <symbol id="ft-i-chev" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="ft-i-arrow" viewBox="0 0 24 24"><path d="M4 12h15M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="ft-i-mail" viewBox="0 0 24 24"><path d="M4.5 6.5h15v11h-15zM4.5 7.5l7.5 5.8 7.5-5.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></symbol>
        <symbol id="ft-i-phone" viewBox="0 0 24 24"><path fill="currentColor" d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25c1.1.37 2.3.57 3.6.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.3.2 2.5.57 3.6a1 1 0 0 1-.25 1z"/></symbol>
        <symbol id="ft-i-pin" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></symbol>
        <symbol id="ft-i-fb" viewBox="0 0 24 24"><path fill="currentColor" d="M13.5 21v-8h2.6l.4-3.1h-3V8c0-.9.3-1.5 1.6-1.5h1.5V3.7c-.3 0-1.2-.1-2.2-.1-2.3 0-3.8 1.4-3.8 3.9v2.4H8v3.1h2.6v8z"/></symbol>
        <symbol id="ft-i-ig" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="4.5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="16.9" cy="7.1" r="1.2" fill="currentColor"/></symbol>
        <symbol id="ft-i-x" viewBox="0 0 24 24"><path fill="currentColor" d="M17.8 4h2.7l-5.9 6.7L21.5 20h-5.4l-4.3-5.6L6.9 20H4.2l6.3-7.2L3.7 4h5.5l3.9 5.1zm-1 14.4h1.5L8.3 5.5H6.7z"/></symbol>
        <symbol id="ft-i-yt" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/></symbol>
        <symbol id="ft-i-in" viewBox="0 0 24 24"><path fill="currentColor" d="M6.5 9h-3v11h3zM5 4a1.8 1.8 0 1 0 0 3.6A1.8 1.8 0 0 0 5 4zm5.5 5H7.6v11h3v-5.6c0-1.6.6-2.6 2-2.6 1.3 0 1.8.9 1.8 2.6V20h3v-6.3c0-3-1.6-4.9-4-4.9-1.3 0-2.3.6-2.9 1.5z"/></symbol>
    </svg>

    <div class="ft-main">
        <span class="ft-mandala ft-mandala--l" aria-hidden="true"></span>
        <span class="ft-mandala ft-mandala--r" aria-hidden="true"></span>
        <span class="ft-wave" aria-hidden="true"></span>

        <div class="ft-container">
            <div class="row gx-0 gy-4 gy-xl-0 ft-columns">
                <div class="col-12 col-md-6 col-xl-auto ft-col ft-col--stay footer-subscribe">
                    <h3 class="ft-heading">Stay Connected</h3>
                    <p class="ft-text">Subscribe for official BRICS updates and cultural events.</p>
                    <div class="ft-form footer-subscribe-form">
                        <input class="form-control ft-input" type="email" placeholder="Enter your email" aria-label="Email address">
                        <button type="button" class="btn ft-btn btn-subscribe">Subscribe <svg class="ft-ico-arrow" aria-hidden="true"><use href="#ft-i-arrow"/></svg></button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-auto ft-col ft-col--brand footer-brand">
                    <a href="{{ url('/') }}" class="ft-logo-link" aria-label="BRICS Home">
                        <img src="{{ asset('assets/frontend/v1/images/brics-logo.svg') }}" width="240" alt="BRICS India 2026" class="ft-logo footer-logo">
                    </a>
                    <span class="ft-orn-rule" aria-hidden="true"></span>
                    <!--p class="ft-tagline">Arts <span aria-hidden="true">&bull;</span> Culture <span aria-hidden="true">&bull;</span> People<br>Stronger Together</p-->
					<p class="ft-tagline">BRICS Artist Registry</p> <br>
                </div>

                <div class="col-6 col-md-4 col-xl-auto ft-col ft-col--quick">
                    <span class="ft-divider" aria-hidden="true"></span>
                    <h3 class="ft-heading">Quick Links</h3>
                    <ul class="list-unstyled ft-links footer-links">
                        @foreach (getMenu()->where('is_footer', 1)->take(4) as $menu)
                            <li><a href="{{ url($menu->url) }}"><span>{{ $menu->title }}</span><svg class="ft-chev" aria-hidden="true"><use href="#ft-i-chev"/></svg></a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-6 col-md-4 col-xl-auto ft-col ft-col--policy">
                    <h3 class="ft-heading">Policies</h3>
                    <ul class="list-unstyled ft-links footer-links">
                        @foreach (getMenu()->where('is_footer', 1)->skip(4) as $menu)
                            <li><a href="{{ url($menu->url) }}"><span>{{ $menu->title }}</span><svg class="ft-chev" aria-hidden="true"><use href="#ft-i-chev"/></svg></a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-12 col-md-4 col-xl-auto ft-col ft-col--contact footer-contact-col">
                    <h3 class="ft-heading">Contact Us</h3>
                    <ul class="list-unstyled ft-contact footer-contact-list">
                        <li>
                            <span class="ft-ico" aria-hidden="true"><svg><use href="#ft-i-mail"/></svg></span>
                            <a href="mailto:bricsartistrepo@gov.in">bricsartistrepo@gov.in</a>
                        </li>
                        <li>
                            <span class="ft-ico" aria-hidden="true"><svg><use href="#ft-i-phone"/></svg></span>
                            <span>+91-11-2307 8000</span>
                        </li>
                        <li>
                            <span class="ft-ico" aria-hidden="true"><svg><use href="#ft-i-pin"/></svg></span>
                            <span>Kartavya Bhavan - 2, New Delhi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @php $latest = lastUpdated(); @endphp

    <div class="ft-bottom">
        <div class="ft-bottom-inner">
		
		
			 @if(!empty($latest) && is_array($latest) && !empty($latest['updated_at']))
                    <span class="ft-updated">Last updated: {{ $latest['updated_at'] }}</span>
                @endif
				
            <div class="ft-copy">
                <span>&copy; {{ date('Y') }} BRICS Artist Registry. All Rights Reserved.</span>
               
            </div>
			
				
            <!--div class="ft-social">
                <a href="#" aria-label="Facebook"><svg aria-hidden="true"><use href="#ft-i-fb"/></svg></a>
                <a href="#" aria-label="Instagram"><svg aria-hidden="true"><use href="#ft-i-ig"/></svg></a>
                <a href="#" aria-label="X (Twitter)"><svg aria-hidden="true"><use href="#ft-i-x"/></svg></a>
                <a href="#" aria-label="YouTube"><svg aria-hidden="true"><use href="#ft-i-yt"/></svg></a>
                <a href="#" aria-label="LinkedIn"><svg aria-hidden="true"><use href="#ft-i-in"/></svg></a>
            </div-->
            <ul class="list-unstyled ft-legal">
                <li><a href="{{ url('sitemap') }}">Sitemap</a></li>
                <li><a href="{{ url('content/accessibility') }}">Accessibility</a></li>
                <!--li><a href="{{ url('content/help') }}">Help</a></li-->
            </ul>
			
			
        </div>
    </div>
</footer>
