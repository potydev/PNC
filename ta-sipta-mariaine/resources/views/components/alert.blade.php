@props(['type' => 'info', 'message'])

@php
    $config = [
        'success' => [
            'bg' => 'bg-green-50',
            'text' => 'text-green-800',
            'dark' => 'dark:text-green-400',
            'icon' => '<svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'text' => 'text-red-800',
            'dark' => 'dark:text-red-400',
            'icon' => '<svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11H9v4h2V7zm0 6H9v2h2v-2z" clip-rule="evenodd"/></svg>',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'text' => 'text-yellow-800',
            'dark' => 'dark:text-yellow-300',
            'icon' => '<svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.6A1.75 1.75 0 0116.518 17H3.482a1.75 1.75 0 01-1.742-2.3l6.517-11.6zM10 13a1 1 0 100 2 1 1 0 000-2zm.75-6.25a.75.75 0 00-1.5 0v4a.75.75 0 001.5 0v-4z" clip-rule="evenodd"/></svg>',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'text' => 'text-blue-800',
            'dark' => 'dark:text-blue-400',
            'icon' => '<svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 7a1 1 0 112 0v1a1 1 0 11-2 0V7zm1-6a9 9 0 100 18A9 9 0 0010 1zm-1 8h2v6h-2V9z"/></svg>',
        ],
        'dark' => [
            'bg' => 'bg-gray-50',
            'text' => 'text-gray-800',
            'dark' => 'dark:text-gray-300',
            'icon' => '<svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9 7a1 1 0 112 0v1a1 1 0 11-2 0V7zm1 3a1 1 0 00-1 1v3h2v-3a1 1 0 00-1-1z"/></svg>',
        ],
    ];

    $style = $config[$type];
@endphp

<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show"
     class="flex items-center justify-between p-4 mb-4 text-sm {{ $style['text'] }} rounded-lg {{ $style['bg'] }} dark:bg-gray-800 {{ $style['dark'] }}"
     role="alert" x-transition>
    <div class="flex items-center">
        {!! $style['icon'] !!}
<span><span class="font-medium">{{ ucfirst($type) }}!</span> {!! $message !!}</span>
    </div>
    <button type="button" @click="show = false" class="ms-2 text-xl font-bold {{ $style['text'] }} hover:opacity-70">
        &times;
    </button>
</div>

