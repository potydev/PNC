@if (session('success'))
    <x-alert type="success" :message="session('success')" />
@endif

@if (session('error'))
    <x-alert type="error" :message="session('error')" />
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <x-alert type="error" :message="$error" />
    @endforeach
@endif
