@extends('layouts.customer')

@section('title', 'Book Repair Appointment')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Book a Repair Appointment</h2>

    <form action="{{ route('customer.appointment.store') }}" method="POST">
        @csrf

        <!-- Step 1: Select Repair Service -->
        <div id="step-1">
            <h4>Step 1: Select Repair Services</h4>
            <div class="row">
                @foreach ($repairServices as $service)
                    <div class="col-md-4 mb-4">
                        <div class="card p-3 shadow-sm">
                            <h5>{{ $service->service_name }}</h5>
                            <p class="text-muted">{{ $service->description }}</p>
                            <p>Price: <strong>${{ $service->price }}</strong></p>
                            <p>Estimated Time: <strong>{{ $service->time_taken }} mins</strong></p>
                            <input type="checkbox" name="service_ids[]" value="{{ $service->service_id }}" class="form-check-input" data-price="{{ $service->price }}" onchange="calculateTotalPrice()">
                            <label class="form-check-label">Select</label>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-primary mt-3" onclick="nextStep(2)">Next</button>
        </div>

        <!-- Step 2: Select Service Method -->
        <div id="step-2" style="display: none;">
            <h4>Step 2: Select Service Method</h4>
            <div class="row">
                @foreach ($serviceMethods as $method)
                    <div class="col-md-4 mb-4">
                        <div class="card p-3 shadow-sm">
                            <h5>{{ $method->method_name }}</h5>
                            <p>Estimated Time: <strong>{{ $method->estimated_time }} mins</strong></p>
                            <p>Additional Fixed Cost: <strong>${{ $method->cost }}</strong></p>
                            <input type="radio" name="method_id" value="{{ $method->method_id }}" data-cost="{{ $method->cost }}" class="form-check-input" onchange="calculateTotalPrice()">
                            <label class="form-check-label">Select</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Display Calculated Total Price -->
            <div class="mt-4">
                <h5>Total Price: <strong id="totalPrice">$0</strong></h5>
                <input type="hidden" name="total_price" id="totalPriceInput">
            </div>

            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next</button>
        </div>

        <!-- Step 3: Select Date & Time Slot -->
        <div id="step-3" style="display: none;">
            <h4>Step 3: Select Date & Time Slot</h4>

            <!-- Date Selection -->
            <div class="mb-3">
                <label for="appointmentDate" class="form-label">Select Appointment Date</label>
                <input type="date" id="appointmentDate" name="appointment_date" class="form-control" required onchange="updateTimeSlots()" min="{{ date('Y-m-d') }}">
            </div>

            <!-- Time Slot Selection -->
            <div class="mb-3">
                <label class="form-label">Available Time Slots</label>
                <div class="row" id="timeSlotsContainer"></div>
            </div>

            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(4)">Next</button>
        </div>

        <!-- Step 4: Review & Edit Customer Contact Details -->
        <div id="step-4" style="display: none;">
            <h4>Step 4: Review & Edit Contact Details</h4>

            <!-- First Name (Disabled) -->
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" value="{{ $customerDetails->first_name }}" disabled>
            </div>

            <!-- Last Name (Disabled) -->
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" value="{{ $customerDetails->last_name }}" disabled>
            </div>

            <!-- Editable Contact Information -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ $customerDetails->email }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ $customerDetails->phone }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Street Name</label>
                <input type="text" id="street_name" name="street_name" class="form-control" value="{{ $customerDetails->street_name }}">
            </div>

            <div class="mb-3">
                <label class="form-label">House Number</label>
                <input type="text" id="house_number" name="house_number" class="form-control" value="{{ $customerDetails->house_number }}">
            </div>

            <div class="mb-3">
                <label class="form-label">City</label>
                <input type="text" id="city" name="city" class="form-control" value="{{ $customerDetails->city }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ $customerDetails->postal_code }}">
            </div>

            <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Back</button>
            <button type="submit" class="btn btn-success">Confirm Appointment</button>
        </div>


    </form>
</div>

<script>
    function nextStep(step) {
        document.getElementById("step-" + (step - 1)).style.display = "none";
        document.getElementById("step-" + step).style.display = "block";
    }

    function prevStep(step) {
        document.getElementById("step-" + (step + 1)).style.display = "none";
        document.getElementById("step-" + step).style.display = "block";
    }

    function calculateTotalPrice() {
        let total = 0;

        document.querySelectorAll('input[name="service_ids[]"]:checked').forEach(service => {
            total += parseFloat(service.getAttribute("data-price"));
        });

        let selectedMethod = document.querySelector('input[name="method_id"]:checked');
        if (selectedMethod) {
            total += parseFloat(selectedMethod.getAttribute("data-cost"));
        }

        document.getElementById("totalPrice").innerText = `$${total.toFixed(2)}`;
        document.getElementById("totalPriceInput").value = total.toFixed(2);
    }

    function updateTimeSlots() {
        const selectedDateInput = document.getElementById("appointmentDate");
        const selectedDate = new Date(selectedDateInput.value);
        const today = new Date();
        const currentHour = today.getHours();
        const selectedMethod = document.querySelector('input[name="method_id"]:checked');
        const timeSlotsContainer = document.getElementById("timeSlotsContainer");

        timeSlotsContainer.innerHTML = ""; // Clear existing slots

        if (!selectedDateInput.value) {
            return; // Ensure the function exits if no date is selected
        }

        let serviceDuration = selectedMethod ? parseInt(selectedMethod.getAttribute("data-duration")) : 60; // Default 60 mins

        const workingHours = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];

        workingHours.forEach(slot => {
            let slotHour = parseInt(slot.split(":")[0]);
            if (slot.includes("PM") && slotHour !== 12) slotHour += 12; // Convert PM to 24-hour format

            // Skip past times if booking for today
            if (selectedDate.toDateString() === today.toDateString() && slotHour <= currentHour) {
                return;
            }

            // Ensure service duration doesn't exceed closing time
            if ((slotHour + serviceDuration / 60) > 18) { // Closing time is 6 PM (18:00)
                return;
            }

            // Generate time slot option
            const slotElement = `<div class="col-md-3">
                <input type="radio" name="time_slot" value="${slot}" class="form-check-input">
                <label class="form-check-label">${slot}</label>
            </div>`;
            timeSlotsContainer.innerHTML += slotElement;
        });
    }

    // Ensure time slots update when a service method is selected
    document.querySelectorAll('input[name="method_id"]').forEach(method => {
        method.addEventListener('change', updateTimeSlots);
    });


</script>
@endsection
