<nav class="fixed top-0 left-0 right-0 z-50 glass-morphism shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            @include('components.navigation.logo')
            @include('components.navigation.desktop-menu')
            @include('components.navigation.mobile-toggle')
        </div>
    </div>
    @include('components.navigation.mobile-menu')
</nav>
