@extends('layouts.app')

@section('title', 'Suivi de Dossier — Espace Client')

@section('content')
{{-- client/suivi.blade.php — alias vers client.ticket --}}
@include('client.ticket')
@endsection
