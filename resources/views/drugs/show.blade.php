<x-layout>

    <div class="row">
        <div class="offset-3 col-6">

            <x-card>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h2 class="card-title">{{ $drug->openfda->brand_name[0] }}</h2>
                        <span class="badge bg-secondary fs-5">{{ $drug->products[0]->marketing_status }}</span>
                        {{-- <span class="badge bg-secondary fs-5">{{ $drug->openfda->product_type[0] == 'HUMAN OTC DRUG' ? 'OTC' : 'Prescription'; }}</span> --}}
                    </div>
                    <h6 class="card-subtitle text-muted mb-2">{{ $drug->openfda->generic_name[0] }}</h2>
                </div>

                <x-drug-info-card-components.drug-info-accordion :drug="$drug"/>
                {{-- <ul class="list-group list-group-flush">
                    <li class="list-group-item">{{  }}</li>
                    <li class="list-group-item">Drug2</li>
                    <li class="list-group-item">Drug3</li>
                </ul> --}}
            </x-card>

        </div>
    </div>

</x-layout>