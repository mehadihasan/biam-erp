@php
    $isActive = old('is_active', $supplier?->is_active ?? true);
@endphp

<div class="supplier-form__grid">
    <label class="supplier-form__field">
        <span>Company Name <span class="supplier-form__required">*</span></span>
        <input name="company_name" type="text" value="{{ old('company_name', $supplier?->company_name) }}" class="supplier-form__control">
        @error('company_name') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Contact Person</span>
        <input name="contact_person" type="text" value="{{ old('contact_person', $supplier?->contact_person) }}" class="supplier-form__control">
        @error('contact_person') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Phone</span>
        <input name="phone" type="text" value="{{ old('phone', $supplier?->phone) }}" class="supplier-form__control">
        @error('phone') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Email</span>
        <input name="email" type="email" value="{{ old('email', $supplier?->email) }}" class="supplier-form__control">
        @error('email') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Category</span>
        <input name="category" type="text" value="{{ old('category', $supplier?->category) }}" placeholder="e.g. Stationery" class="supplier-form__control">
        @error('category') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field supplier-form__field--wide">
        <span>Address</span>
        <textarea name="address" rows="3" class="supplier-form__control">{{ old('address', $supplier?->address) }}</textarea>
        @error('address') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__check">
        <input name="is_active" type="hidden" value="0">
        <input name="is_active" type="checkbox" value="1" @checked((bool) $isActive)>
        <span>Active</span>
    </label>
</div>
