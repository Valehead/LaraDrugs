<x-layout>

    <div class="row">
        <div class="col-8">

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
        <x-search-result-table-components.review-section></x-search-result-table-components.review-section>
    </div>

</x-layout>


