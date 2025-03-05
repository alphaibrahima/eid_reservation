<div class="step-indicator mb-5">
    <div class="step-progress" id="step-progress"></div>
    <div class="step-dots">
        @foreach([1, 2, 3, 4] as $step)
        <div class="step-dot {{ $step === 1 ? 'active' : '' }}" data-step="{{ $step }}"></div>
        @endforeach
    </div>
</div>