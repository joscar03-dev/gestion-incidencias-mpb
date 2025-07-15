@extends('layouts.welcome')

@section('title', 'Dashboard - Sistema de Gestión de Incidencias')

@section('content')
    <!-- Dashboard SPA para usuarios autenticados -->
    @livewire('dashboard')
@endsection
