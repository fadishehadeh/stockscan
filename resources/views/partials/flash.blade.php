@if (session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="flash-error">
        <p class="font-semibold">Please fix the following:</p>
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
