@if (session()->has('message'))
    <div 
        class="
            alert alert-danger position-absolute top-0 start-50 translate-middle-x
            shadow p-3 mt-2 rounded" 
        role="alert"
    >
        <p>{{ session('message') }}</p>
    </div>
@endif