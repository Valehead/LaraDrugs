<x-layout>

    <div class="row">
        <div class="offset-3 col-6">

            <x-card>
                <h2 class="card-title">{{ $drug[0]->openfda->brand_name[0] }}</h2>
                <h6 class="card-subtitle text-muted">{{ $drug[0]->openfda->generic_name[0] }}</h2>
            </x-card>

        </div>
    </div>

</x-layout>
