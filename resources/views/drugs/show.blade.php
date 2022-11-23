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
    <div class="m-3">
        <x-card>
            <div class="col">
                <div class="col container-sm mt-3">Adverse Events Input
                    <div class="input-group">
                        <span class="input-group-text">comment (optional)</span>
                        <textarea class="form-control" aria-label="Comments"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="input-group m-3 p-3">
                        <label class="form-label" for="customRange">satisfaction scale:</label>
                    </div>

                    <div class="range position-relative col w-10 m-2">
                        <input type="range" class="form-range" min="0" max="10" id="customRange"/>


                        <button type="button" class="btn btn-outline-primary">Primary</button>

                    </div>
                </div>
            </div>
        </x-card>

    </div>

</x-layout>


