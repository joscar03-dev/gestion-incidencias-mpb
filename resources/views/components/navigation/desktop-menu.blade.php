<div class="hidden md:flex items-center space-x-4">
    @auth
        <div class="flex items-center space-x-4">
            @include('components.navigation.user-info')
            @include('components.navigation.notifications')
            @include('components.navigation.auth-links')
        </div>
    @else
        @include('components.navigation.guest-links')
    @endauth
    @include('components.navigation.theme-toggle')
</div>
