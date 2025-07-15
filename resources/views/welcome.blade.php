@extends('layouts.welcome')

@section('title', 'Centro de Soporte - Sistema de Gestión de Incidencias')

@section('content')
    <!-- Landing Page para todos los usuarios -->
    <x-hero />
    <x-quick-report />
    <x-user-stats />
    <x-service-categories />
    <x-faq />
    <x-system-status />
@endsection
