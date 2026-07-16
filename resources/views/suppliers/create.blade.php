@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Supplier
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan informasi supplier baru.
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('suppliers.store') }}">

                @csrf

                @include('suppliers._form', [
                    'buttonText' => 'Simpan Supplier',
                ])
            </form>
        </div>

    </div>
@endsection
