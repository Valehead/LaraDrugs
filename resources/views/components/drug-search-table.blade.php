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
                @foreach ($applications as $application)
                    @php
                        $appnum=$application->application_number;
                    @endphp

                    @foreach ($application->products as $drug)

                    <x-search-result-table-components.drug-row :drug="$drug" :iteration="$loop->parent->iteration" :appnum="$appnum"/>

                    @endforeach

                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
