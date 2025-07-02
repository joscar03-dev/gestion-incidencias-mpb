@if (isset($data))
    <script>
        window.filamentData = @js($data)
    </script>
@endif

@foreach ($assets as $asset)
    @if (! $asset->isLoadedOnRequest())
        {{ $asset->getHtml() }}
    @endif
@endforeach

<style>
    :root {
        @foreach ($cssVariables ?? [] as $cssVariableName => $cssVariableValue) --{{ $cssVariableName }}:{{ $cssVariableValue }}; @endforeach
    }
</style>
<audio id="noti-sound-badge" src="/sonidos/notification.mp3" preload="auto"></audio>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function observeBadge() {
        const badge = document.querySelector('.fi-topbar-database-notifications-btn .fi-badge .truncate');
        if (!badge) {
            setTimeout(observeBadge, 500);
            return;
        }
        let lastValue = parseInt(badge.textContent) || 0;

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                const newValue = parseInt(badge.textContent) || 0;
                if (newValue > lastValue) {
                    const audio = document.getElementById('noti-sound-badge');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
                lastValue = newValue;
            });
        });

        observer.observe(badge, { childList: true, characterData: true, subtree: true });
    }
    observeBadge();
});
</script>
