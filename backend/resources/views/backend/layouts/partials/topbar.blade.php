<header class="header-admin py-2 bg-white shadow-sm d-flex align-items-center">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <a target="_blank" href="{{route('home')}}"><img src="{{ asset('assets/backend/v1/images/logo.png') }}" class="logo"
                                alt="National Overseas Scholarship Scheme logo" /></a>
                        <button type="button" class="p-0 bg-transparent fs-2 text-black main-menu"
                            aria-label="Open main menu" aria-controls="main-menu" tabindex="0" aria-expanded="false">
                            <i class="bi bi-list" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="col-xxl-10 col-xl-9 col-lg-8 col-md-7">
                    <nav aria-label="Header navigation" id="primary-navigation">
                        <ul class="d-flex align-items-center justify-content-end list-unstyled mb-0 gap-3">
                            <li><a href="#maincontent" aria-label="Skip to Main Content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="28" viewBox="0 0 64 64"
                                        fill="#000">
                                        <path fill="#000"
                                            d="M57 10H14.5C13.1739 10 11.9021 10.5268 10.9645 11.4645C10.0268 12.4021 9.5 13.6739 9.5 15V25H14.5V15H57V50H14.5V40H9.5V50C9.5 51.3261 10.0268 52.5979 10.9645 53.5355C11.9021 54.4732 13.1739 55 14.5 55H57C58.3261 55 59.5979 54.4732 60.5355 53.5355C61.4732 52.5979 62 51.3261 62 50V15C62 13.6739 61.4732 12.4021 60.5355 11.4645C59.5979 10.5268 58.3261 10 57 10ZM19.5 40V35H2V30H19.5V25L29.5 32.5L19.5 40ZM52 35H34.5V30H52V35ZM52 25H34.5V20H52V25ZM44.5 45H34.5V40H44.5V45Z">
                                        </path>
                                    </svg>
                                </a></li>
                            <!--li>
                                <div class="btn-group">
                                    <button type="button" class="bg-transparent fs-3 position-relative"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        aria-label="Notifications"><i class="bi bi-bell" aria-hidden="true"></i> <span
                                            class="notification-count position-absolute">10</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-lg-end notifications">
                                        <div
                                            class="notification-heading py-2 px-3 bg-blue text-white d-flex align-items-center justify-content-between">
                                            Notifications <button type="button" class="btn-close text-white"
                                                aria-label="Close"></button></div>
                                        <div class="notification-content">
                                            <ul class="">
                                                <li><a href="#" class="d-flex align-items-start gap-2 mb-2"><i
                                                            class="bi bi-stop-fill" aria-hidden="true"></i> Decorative
                                                        icons are hidden from screen readers</a></li>
                                                <li><a href="#" class="d-flex align-items-start gap-2 mb-2"><i
                                                            class="bi bi-stop-fill" aria-hidden="true"></i> Decorative
                                                        icons are hidden from screen readers</a></li>
                                                <li><a href="#" class="d-flex align-items-start gap-2 mb-2"><i
                                                            class="bi bi-stop-fill" aria-hidden="true"></i> Decorative
                                                        icons are hidden from screen readers</a></li>
                                            </ul>
                                            <!-- <p class="mb-0 text-center d-flex justify-content-center h-100 flex-column">No new notifications</p> -->
                                        <!--/div>
                                    </div>
                                </div>
                            </li-->
							
							
                            <li>





			<div class="dropdown float-end border-0">

    <button
        class="bg-transparent form-select shadow-none border-0 d-flex align-items-center gap-1 text-start lh-1 fw-bold"
        style="border-left:0 !important"
        type="button"
        id="btnGroupDrop3"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        aria-label="User account menu">

        <i class="bi bi-person-circle fs-3"></i>



		<span class="user-name d-flex flex-column lh-sm">
		<span class="fw-semibold">
			{{ Auth::user()->full_name }}
		</span>

		@if(Auth::user()->role)
			<small class="text-muted fw-normal" style="font-size: 11px;">
				{{ Auth::user()->role->name }}
			</small>
		@endif
	</span>

    </button>


    <ul class="dropdown-menu dropdown-menu-end border mt-2 user-dropdown rounded-5"
        aria-labelledby="btnGroupDrop3">

        {{-- Department --}}
        @if(!empty(Auth::user()->department))
            <li>
                <span class="dropdown-item d-flex align-items-center fs-7 text-black">
                    <i class="bi bi-building fs-5 me-2"></i>
                    <b>Dept</b>&nbsp;-&nbsp;{{ Auth::user()->department }}
                </span>
            </li>
        @endif


        {{-- Mobile --}}
        @if(!empty(Auth::user()->mobile))
            <li>
                <span class="dropdown-item d-flex align-items-center fs-7 text-black">
                    <i class="bi bi-telephone fs-5 me-2"></i>
                    <b>Phone</b>&nbsp;-&nbsp;{{ Auth::user()->mobile }}
                </span>
            </li>
        @endif


        {{-- Email --}}
        @if(!empty(Auth::user()->email))
            <li>
                <span class="dropdown-item d-flex align-items-center fs-7 text-black">
                    <i class="bi bi-envelope fs-5 me-2"></i>
                    <b>Email</b>&nbsp;-&nbsp;{{ Auth::user()->email }}
                </span>
            </li>
        @endif


        {{-- Divider --}}
        <li>
            <hr class="dropdown-divider">
        </li>


        {{-- Update Profile --}}
        <li>
            <a class="dropdown-item d-flex align-items-center fs-7 text-black"
               href="{{ route('profile.edit') }}">

                <i class="bi bi-person-circle fs-5 me-2"></i>
                <span>Update Profile</span>

            </a>
        </li>


        {{-- Change Password --}}
        <li>
            <a class="dropdown-item d-flex align-items-center fs-7 text-black"
               href="{{ route('password.edit') }}">

                <i class="bi bi-pencil-square fs-5 me-2"></i>
                <span>Change Password</span>

            </a>
        </li>


        {{-- Sign Out --}}
        <li>
            <a class="dropdown-item d-flex align-items-center fs-7 text-danger"
               href="{{ url('logout') }}">

                <i class="bi bi-box-arrow-right fs-5 me-2"></i>
                <span>Sign Out</span>

            </a>
        </li>

    </ul>
</div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>
