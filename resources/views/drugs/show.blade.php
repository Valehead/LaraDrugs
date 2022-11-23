<x-layout>

    <div class="row">
        <div class="offset-md-1 col-md-10 offset-3 col-6">

            <x-card>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h2 class="card-title">{{ $drug->products[0]->brand_name }}</h2>
                        <span class="badge bg-secondary fs-5">{{ $drug->products[0]->marketing_status }}</span>

                    </div>

                    @if (isset($drug->openfda->generic_name[0]))
                        <h6 class="card-subtitle text-muted mb-2">{{ $drug->openfda->generic_name[0] }}</h6>
                    @endif

                </div>

                <x-drug-info-card-components.drug-info-accordion :drug="$drug" :druginfo="$druginfo"/>

            </x-card>

        </div>
    </div>

    <x-card>
    <div class="container-md m-3">100% wide until medium breakpoint
        <div class="input-group">
            <span class="input-group-text">With textarea</span>
            <textarea class="form-control" aria-label="With textarea"></textarea>
        </div>

        <button type="button" class="btn btn-outline-primary">Primary</button>
    </div>
    </x-card>

</x-layout>


