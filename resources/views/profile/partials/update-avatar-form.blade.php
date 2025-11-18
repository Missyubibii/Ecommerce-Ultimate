<!-- resources/views/profile/partials/update-avatar-form.blade.php -->
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Avatar') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your profile avatar.') }}
        </p>
    </header>

    <div class="mt-6 flex items-center">
        @if(Auth::user()->avatar)
            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="User Avatar" class="h-20 w-20 rounded-full object-cover">
        @else
            <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-gray-600 text-xl font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
        @endif

        <div class="ml-6">
            <form method="POST" action="{{ route('profile.avatar.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center">
                    <label for="avatar" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Choose File') }}
                    </label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="sr-only" @change="previewAvatar(event)">
                    <span id="file-name" class="ml-3 text-sm text-gray-500">{{ __('No file chosen') }}</span>
                </div>

                <div class="mt-3">
                    <img id="avatar-preview" src="#" alt="Avatar Preview" class="h-20 w-20 rounded-full object-cover hidden">
                </div>

                <div class="mt-4">
                    <x-primary-button>{{ __('Upload Avatar') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @if (session('status') === 'avatar-updated')
        <p
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 2000)"
            class="mt-2 text-sm text-gray-600"
        >{{ __('Avatar updated.') }}</p>
    @endif
</section>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    const fileName = document.getElementById('file-name');
    const preview = document.getElementById('avatar-preview');

    if (file) {
        fileName.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        fileName.textContent = '{{ __('No file chosen') }}';
        preview.classList.add('hidden');
    }
}
</script>
