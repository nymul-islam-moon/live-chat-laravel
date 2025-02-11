<!-- chat.blade.php -->

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<x-app-layout>
    @livewire('chat-component', ['user_id' => $id])
</x-app-layout>
