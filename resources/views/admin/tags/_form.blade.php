<div class="field"><label for="name">Name *</label><input id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" maxlength="255" required></div>
<div class="actions"><button class="button" type="submit">{{ $submitLabel }}</button><a class="button secondary" href="{{ route('admin.tags.index') }}">Cancel</a></div>
