<div class="p-4 bg-white rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-4">Test Livewire Component</h2>

    <!-- Message Display -->
    <div class="mb-4 p-3 bg-green-100 rounded">
        <p class="text-green-800">{{ $message }}</p>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-xl">Count: {{ $count }}</span>
        <button wire:click="increment" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Increment
        </button>
    </div>

    <!-- Debug info -->
    <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
        <p>Debug: Component loaded successfully</p>
        <p>Current count: {{ $count }}</p>
        <p>Component ID: {{ $this->getId() }}</p>
        <p>CSRF Token: {{ csrf_token() }}</p>
    </div>

    <!-- Alternative button for testing -->
    <div class="mt-4">
        <button onclick="window.Livewire.find('{{ $this->getId() }}').call('increment')"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            Alternative Increment (Direct JS)
        </button>
    </div>
</div>
