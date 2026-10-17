<div class="ad-field full">
    <label class="ad-label" for="f-name">Nom</label>
    <input id="f-name" type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required>
    @error('name') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <label class="ad-label" for="f-desc">Description</label>
    <textarea id="f-desc" name="description" rows="4">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <span class="ad-label">Photo</span>
    @if(!empty($category->image ?? null))
        <img src="{{ asset('storage/'.$category->image) }}" class="ad-thumb-lg ad-mb-s" alt="">
    @endif
    <label class="ad-drop">
        <x-ad-icon name="upload" :size="24" />
        <span><b>Choisir une image</b></span>
        <input type="file" name="image" accept="image/*" data-preview="ad-previews">
    </label>
    <div class="ad-previews" id="ad-previews"></div>
    @error('image') <p class="ad-error">{{ $message }}</p> @enderror
</div>
