<div class="step-indicator mb-5">
    <div class="step-progress" style="width: {{ $progress }}%"></div>
    <div class="step-dots">
        @foreach([1,2,3,4] as $step)
            <div class="step-dot 
                {{ $currentStep == $step ? 'active' : '' }}
                {{ $currentStep > $step ? 'completed' : '' }}
                " data-step="{{ $step }}">
            </div>
        @endforeach
    </div>
</div>