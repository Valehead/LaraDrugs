<x-card>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Brand Name</th>
                    <th scope="col">AppNumber</th>
                    <th scope="col">Dosage Form</th>
                    <th scope="col">Route</th>
                    <th scope="col">Link</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $numOfDrugs = 1;
                @endphp
                @foreach ($applications as $application)

                    <?php $appnum=$application->application_number; ?>

                    @if (count($application->products) > 1)
                        @php

                            if($loop->iteration == 1)$accordionID = 0;
                            $accordionID++;
                        @endphp
                            <tr data-bs-toggle="collapse" href="#accordion{{ $accordionID }}" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <th scope="row">#</th>
                                <td colspan="6">
                                    {{ isset($application->openfda) ? $application->openfda->brand_name[0] : $application->products[0]->brand_name }}
                                </td>
                            </tr>

                            <tr style="border-bottom: hidden">
                                <td colspan="6" style="padding: 0 !important; overflow: hidden;">
                                    <div class="accordion-body collapse" id="accordion{{ $accordionID }}">
                                        <table class="table table-striped m-2">
                                            <thead>
                                                <tr class="bg-info">
                                                    <th scope="col">#</th>
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Dosage Size</th>
                                                    <th scope="col">Dosage Form</th>
                                                    <th scope="col">Links</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Product1</td>
                                                    <td>1mg</td>
                                                    <td>Tablet</td>
                                                    <td><a href="#">Link</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>

                        @foreach ($application->products as $drug)

                            <x-search-result-table-components.drug-row :drug="$drug" :iteration="$numOfDrugs" :appnum="$appnum"/>
                            @php
                                $numOfDrugs++;
                            @endphp

                        @endforeach

                    @else

                        <x-search-result-table-components.drug-row :drug="$application->products[0]" :iteration="$numOfDrugs" :appnum="$appnum"/>

                    @endif


                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
