$(document).ready(function () {
  /* ==================================================
       SIDEBAR / MENU LOGIC
    ================================================== */
  const $body = $("body");
  const $menuToggleBtn = $(".main-menu");

  /* ---- URL BASED ACTIVE MENU ---- */
  const currentPage = window.location.pathname.split("/").pop();

  $(".side-navigation a[href]").each(function () {
    const href = $(this).attr("href");

    if (href && href === currentPage) {
      const $link = $(this);

      $link.closest("li").addClass("active");

      const $submenu = $link.closest(".submenu");
      if ($submenu.length) {
        const $toggleBtn = $submenu.prev(".submenu-toggle");

        $submenu.addClass("show");
        $toggleBtn
          .attr("aria-expanded", "true")
          .find(".bi-chevron-down")
          .addClass("rotate");

        $toggleBtn.closest(".menu-item").addClass("active");
      } else {
        $link.closest(".menu-item").addClass("active");
      }
    }
  });

  /* ---- SUBMENU ACCORDION ---- */
  $(".submenu-toggle").on("click", function (e) {
    e.stopPropagation();

    if ($body.hasClass("sidebar-collapsed")) return;

    const $btn = $(this);
    const expanded = $btn.attr("aria-expanded") === "true";
    const $currentSubmenu = $btn.next(".submenu");

    $(".submenu-toggle")
      .not($btn)
      .each(function () {
        $(this)
          .attr("aria-expanded", "false")
          .find(".bi-chevron-down")
          .removeClass("rotate");

        $(this).next(".submenu").removeClass("show");
      });

    $btn.attr("aria-expanded", !expanded);
    $currentSubmenu.toggleClass("show", !expanded);
    $btn.find(".bi-chevron-down").toggleClass("rotate", !expanded);
  });

  /* ---- SIDEBAR TOGGLE ---- */
  if ($menuToggleBtn.length) {
    $menuToggleBtn.on("click", function (e) {
      e.stopPropagation();

      const isMobile = $(window).width() <= 992;

      if (isMobile) {
        $body.toggleClass("sidebar-open");
        $(this).attr("aria-expanded", $body.hasClass("sidebar-open"));
      } else {
        $body.toggleClass("sidebar-collapsed");
        const isCollapsed = $body.hasClass("sidebar-collapsed");

        $(this).attr("aria-expanded", !isCollapsed);

        if (isCollapsed) {
          $(".submenu").removeClass("show");
          $(".submenu-toggle")
            .attr("aria-expanded", "false")
            .find(".bi-chevron-down")
            .removeClass("rotate");
        }
      }
    });
  }

  /* ---- CLOSE SIDEBAR ON OUTSIDE CLICK (MOBILE) ---- */
  $(document).on("click", function (e) {
    if (
      $body.hasClass("sidebar-open") &&
      !$(e.target).closest(".side-navigation").length &&
      !$(e.target).closest(".main-menu").length
    ) {
      $body.removeClass("sidebar-open");
      $menuToggleBtn.attr("aria-expanded", "false");
    }
  });

});
