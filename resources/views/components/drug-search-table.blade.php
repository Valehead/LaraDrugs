<x-card>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Brand Name</th>
                    <th scope="col">Generic Name</th>
                    <th scope="col">Dosage Form</th>
                    <th scope="col">Route</th>
                    <th scope="col">Link</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($drugs as $drug)

                    <x-search-result-table-components.drug-row :drug="$drug" :iteration="$loop->iteration"/>

                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
