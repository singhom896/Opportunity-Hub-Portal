<!doctype html>
<html lang="en">
@include('frontend.layouts.partials.header')
<body class="@yield('body-class')">
@include('frontend.layouts.partials.nav')
<main class="overflow-x-hidden">
    @yield('content')
</main>
@include('frontend.layouts.partials.footer')
<script src="{{ asset('assets/frontend/v1/js/jquery-3.6.0.min.js') }}"></script>

<!--script src="{{ asset('assets/vendor/v1/validate/jquery.validate.js') }}"></script-->


<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>


<script src="{{ asset('assets/vendor/v1/validate/additional-methods.js') }}">  </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://translation-plugin.bhashini.co.in/v3/website_translation_utility.js"></script>
<script src="https://img1.digitallocker.gov.in/ux4g/UX4G-CDN-accessibility/js/weights-v1.js"></script>
<script type="text/javascript">
    document.getElementById("uw-widget-custom-trigger2").addEventListener("click", function () {
        openMain();
    });
</script>
@stack('scripts')
</body>
</html>

