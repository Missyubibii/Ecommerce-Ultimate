<section>
    <div class="flex flex-col items-center text-center">
        <div class="relative group">
            {{-- Avatar hiện tại --}}
            @if(Auth::user()->avatar)
                <img id="avatar-preview-main" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
            @else
                <div id="avatar-placeholder-main"
                    class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-4xl font-bold border-4 border-white shadow-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <img id="avatar-preview-main" src="#"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg hidden">
            @endif

            {{-- Nút upload đè lên --}}
            <label for="avatar-upload"
                class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full shadow-md cursor-pointer hover:bg-indigo-700 transition transform hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </label>
        </div>

        <form id="avatar-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
            class="mt-4 w-full">
            @csrf
            @method('patch')

            {{-- Hidden Input --}}
            <input id="avatar-upload" name="avatar" type="file" accept="image/*" class="hidden"
                onchange="previewAndSubmitAvatar(event)">

            {{-- Chúng ta cần gửi kèm name/email để bypass validation required của ProfileUpdateRequest --}}
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="email" value="{{ Auth::user()->email }}">

            <p class="text-xs text-gray-500 mt-2">Cho phép: JPG, JPEG, PNG. Tối đa 2MB.</p>

            {{-- Nút Save sẽ hiện ra khi chọn ảnh --}}
            <button id="btn-save-avatar" type="submit"
                class="hidden mt-3 bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-indigo-700">Lưu
                ảnh</button>
        </form>
    </div>

    <script>
        function previewAndSubmitAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Ẩn placeholder, hiện ảnh preview
                    const placeholder = document.getElementById('avatar-placeholder-main');
                    if (placeholder) placeholder.classList.add('hidden');

                    const img = document.getElementById('avatar-preview-main');
                    img.src = e.target.result;
                    img.classList.remove('hidden');

                    // Hiện nút lưu
                    document.getElementById('btn-save-avatar').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</section>
