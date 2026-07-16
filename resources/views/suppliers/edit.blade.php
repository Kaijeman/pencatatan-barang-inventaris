@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Supplier
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi supplier
                <span class="font-semibold text-slate-700">
                    {{ $supplier->name }}
                </span>.
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('suppliers.update', $supplier) }}">

                @csrf
                @method('PUT')

                @include('suppliers._form', [
                    'buttonText' => 'Simpan Perubahan',
                ])
            </form>
        </div>

    </div>
@endsection
