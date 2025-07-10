@extends('layouts.welcome')

@section('title', 'Centro de Soporte - Sistema de Gestión de Incidencias')

@section('content')
    @auth
        <!-- Dashboard SPA -->
        @livewire('dashboard')
    @else
        <!-- Landing Page para usuarios no autenticados -->
        <x-hero />
        <x-quick-report />
        <x-user-stats />
        <x-service-categories />
        <x-faq />
        <x-system-status />
    @endauth
@endsection
