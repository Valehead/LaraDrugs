
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
                                <label for="drugName" class="form-label">Drug Name</label>
                                <input type="text" id="drugName" name="drugName" class="form-control"
                                    placeholder="Search by Generic or Brand name..."
                                    value="{{ old('drugName') }}" autofocus>
                            </div>

                            <div class="mb-3">
                                <x-form-components.drug-class-select />
                            </div>

                            <div class="mb-3">
                                <label for="symptomName" class="form-label">Symptom</label>
                                <input type="text" id="symptomName" name="symptom" class="form-control" autofocus disabled>
                            </div>

                            <div class="mb-3">
                                <label for="limitCount" class="form-label">Count Limit</label>
                                <input type="number" id="limitCount" value="20" name="count" class="form-control">
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
