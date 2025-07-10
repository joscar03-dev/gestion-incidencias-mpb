<div class="text-sm text-gray-700 dark:text-gray-300">
    <span class="font-medium">{{ auth()->user()->name }}</span>
    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ auth()->user()->area->nombre ?? 'Sin área' }}</span>
</div>
