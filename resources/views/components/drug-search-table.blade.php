<x-card>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Medication</th>
                    <th scope="col">AppNumber</th>
                    <th scope="col">Dosage Form</th>
                    <th scope="col">Route</th>
                    <th scope="col">Link</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $numOfDrugs = 1;
                    $accordionID = 0;
                @endphp

                @foreach ($applications as $application)

                    <?php $appnum=$application->application_number; ?>

                    @if (count($application->products) > 1)
                        @php
                            $accordionID++;
                        @endphp

                        <tr data-bs-toggle="collapse" href="#accordion{{ $accordionID }}" role="button" aria-expanded="false" x-data="{ colored: false }" x-on:click="colored = ! colored" x-bind:style="colored && 'background-color: #eeeee4'">
                            <th scope="row">~</th>
                            <td class="fw-bold">
                                {{ isset($application->openfda) ? $application->openfda->brand_name[0] : $application->products[0]->brand_name }}
                            </td>
                            <td colspan="1">{{ $appnum }}</td>
                            <td>Click to See More</td>
                            <td>V</td>
                            <td></td>
                        </tr>

                        <tr style="border-bottom: hidden">
                            <td colspan="6" style="padding: 0 !important; overflow: hidden;">
                                <div class="accordion-body collapse" id="accordion{{ $accordionID }}">

                                    <table class="table table-hover m-2">
                                        <thead>
                                            <tr class="">
                                                <th scope="col"></th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Active Ingredients</th>
                                                <th scope="col">Form</th>
                                                <th scope="col">Route</th>
                                                <th scope="col">Links</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <tr>
                                                <?php asort($application->products); ?>

                                                @foreach ($application->products as $drug)
                                                    <?php if($drug->marketing_status === "Discontinued") continue; ?>

                                                    <x-search-result-table-components.drug-row :drug="$drug" :iteration="$numOfDrugs" :appnum="$appnum" :appAccordionBool="true"/>
                                                    @php
                                                        $numOfDrugs++;
                                                    @endphp

                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </td>
                        </tr>


                    @else

                        <x-search-result-table-components.drug-row :drug="$application->products[0]" :iteration="$numOfDrugs" :appnum="$appnum" :appAccordionBool="false"/>
                            @php
                                $numOfDrugs++;
                            @endphp

                    @endif


                @endforeach
            </tbody>
        </table>
    </div>
</x-card>

