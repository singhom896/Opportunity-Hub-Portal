
@if ($menu->childrenRecursive->isEmpty())
    <li role="none"><a role="menuitem" href="{{ $menu->is_pdf_menu == 1 ? asset($menu->pdf_url) : url($menu->url) }}" target="{{ $menu->target ?? '_self' }}">{{ $menu->title }}</a></li>
@else
    <li class="dropdown-submenu">
        <a class="dropdown-toggle" href="javascript:void(0)" aria-haspopup="true" aria-expanded="false">{{ $menu->title }} <span class="dropdown-arrow">⌄</span></a>
        <ul class="dropdown-menu" role="menu">
            @foreach ($menu->childrenRecursive as $child)
                @include('frontend.layouts.partials.menu-item', ['menu' => $child])
            @endforeach
        </ul>
    </li>
@endif

