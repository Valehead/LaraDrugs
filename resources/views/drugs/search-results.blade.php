@props(['drugs'])

<x-layout>

    <div class="container">


        <div class="row">


            <div class="col-12">


                    <x-drug-search-table :applications="$drugs"/>


            </div>


        </div>


    </div>

</x-layout>
