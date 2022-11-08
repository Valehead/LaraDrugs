@props(['drug'])

<div class="accordion accordion-flush border-top" id="accordionFlushExample">
    <div class="accordion-item">
      <h2 class="accordion-header" id="flush-headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
          Medication Details
        </button>
      </h2>
      <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
        <div class="accordion-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><span class="fw-bold">Medication Status:</span> {{ $drug->products[0]->marketing_status }}</li>

                {{-- determine if there is more than one ingredient, use blade component if so --}}
                @if (count($drug->products[0]->active_ingredients) == 1)

                    <li class="list-group-item"><span class="fw-bold">Active Ingredients:</span> {{ $drug->products[0]->active_ingredients[0]->name . ' - ' . $drug->products[0]->active_ingredients[0]->strength}}</li>

                @else

                    <x-drug-info-card-components.active-ingredients :ingredients="$drug->products[0]->active_ingredients" />

                @endif

                <li class="list-group-item"><span class="fw-bold">Dosage Form:</span> {{ $drug->products[0]->dosage_form }}</li>
                <li class="list-group-item"><span class="fw-bold">Intake:</span> {{ $drug->products[0]->route }}</li>
            </ul>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="flush-headingTwo">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
          Side Effects
        </button>
      </h2>
      <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
        <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the second item's accordion body. Let's imagine this being filled with some actual content.</div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="flush-headingThree">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
          Manufacturer details
        </button>
      </h2>
      <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
        <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the third item's accordion body. Nothing more exciting happening here in terms of content, but just filling up the space to make it look, at least at first glance, a bit more representative of how this would look in a real-world application.</div>
      </div>
    </div>
  </div>
