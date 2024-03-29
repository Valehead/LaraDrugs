
<x-layout>

        <div class="row">

            <div class="col-4 offset-4">
                <x-card>
                    <div class="card-body">
                        <form method="POST" action="/drugs">
                            @csrf

                            <div class="mb-3 text-center">
                                <h3 class="card-title">
                                    Drug Search Form
                                </h3>
                            </div>

                            <div class="mb-3">
                                <label for="drugName" class="form-label">Medication Name</label>
                                <input type="text" id="drugName" name="drugName" class="form-control"
                                    placeholder="Search by Brand name..."
                                    value="{{ old('drugName') }}" autofocus>
                            </div>

                            <div class="mb-3">
                                <x-form-components.drug-class-select />
                            </div>


                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Search!</button>
                            </div>

                        </form>
                    </div>
                </x-card>
            </div>
        </div>

</x-layout>
