@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('icon', 'ri-shield-keyhole-line')
@section('badge-icon', 'ri-lock-line')
@section('icon-bg', 'bg-danger-subtle text-danger')

@section('message')
    {{ $exception->getMessage() ?: 'Mohon maaf, Anda tidak memiliki hak akses atau izin yang sesuai untuk membuka halaman atau sumber daya ini.' }}
@endsection
