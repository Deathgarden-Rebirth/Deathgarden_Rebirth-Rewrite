@php
    use App\Enums\Launcher\FileAction;
    use App\Enums\Launcher\Patchline;

    /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\GameFile> $files */
    /** @var Patchline $patchline */
@endphp

<x-layouts.admin>
    <div class="w-full p-2 md:px-16 bg-inherit container mx-auto">
        <h1 class="text-4xl font-semilight py-10">Deathgarden file manager</h1>

        <form action="{{ url()->current() }}" method="GET">
            <div class="flex flex-row items-center">
                <label for="patchlines" class="block my-4 mr-4 font-medium text-gray-900 dark:text-white">Select a
                    patchline:</label>
                <x-inputs.dropdown
                        id="patchlines"
                        required
                        name="patchline"
                        :cases="Patchline::cases()"
                        :selected="$patchline"
                        onchange="this.form.submit()"
                />
            </div>
        </form>
        <div class="flex flex-col items-center justify-center">
            <table class="">
                <thead>
                <th>Name</th>
                <th>Hash</th>
                <th>Size</th>
                <th>Last update</th>
                <th>Actions</th>
                </thead>
                <tbody>
                @foreach($files as $file)
                    <tr @class([
                                'text-center',
                                '!bg-green-600 hover:!bg-green-500' => $file->action === FileAction::ADD,
                                '!bg-red-600 hover:!bg-red-500' => $file->action === FileAction::DELETE
                            ])
                    >
                        <td title="{{$file->game_path}}">
                            {{ $file->name }}
                        </td>
                        <td>
                            {{ $file->hash }}
                        </td>
                        <td>
                            @if($file->fileExists())
                                {{ round($file->getFileSize() / 1000, 1) }} kB
                            @else
                                Error while fetching file
                            @endif
                        </td>
                        <td>
                            {{ $file->updated_at }}
                        </td>
                        <td>
                            <form action="{{ route('file-manager.update', ['file_manager' => $file->id]) }}"
                                  method="POST">
                                @method('PUT')
                                @csrf
                                <button class="">Mark for {{ $file->action->value ? 'delete' : 'add' }}</button>
                            </form>
                            <form action="{{ route('file-manager.destroy', ['file_manager' => $file->id]) }}"
                                  method="POST">
                                @method('DELETE')
                                @csrf
                                <button class=""
                                        onclick="return confirm('This will delete the file on the server, but won\'t do any actions on the clients.\nAre you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mx-auto w-full max-w-screen-2xl mt-8">
                <form action="{{ route('file-manager.store') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="patchline" value="{{ request()->input('patchline') ?? '0' }}">
                    @csrf
                    <div id="fileInputsContainer">
                        <div class="flex flex-wrap gap-4">
                            <div class="w-2/5 flex items-center mb-2">
                                <x-inputs.text-input name="game_path[]"/>
                            </div>
                            <select name="file_action[]"
                                    class="w-32 rounded-md h-[41.43px] bg-gray-800/75 border border-gray-600 text-white text-sm focus:border-[#6A64F1] focus:shadow-md block px-4 py-2">
                                <option value="1" selected>Add</option>
                                <option value="0">Delete</option>
                            </select>
                            <div class="flex items-center mb-2">
                                <x-inputs.file-input name="files[]"/>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <div class="w-auto mx-2">
                            <button type="button" id="addFileInput"
                                    class="inline-flex rounded-md bg-gray-800/75 px-6 py-2 font-semibold text-gray-300 hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#BD92F5] focus-visible:ring-offset-2 focus-visible:ring-offset-black">
                                Add More Files
                            </button>
                        </div>
                        <div class="w-auto mx-2">
                            <button class="inline-flex rounded-md bg-gray-800/75 px-6 py-2 font-semibold text-gray-300 hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#BD92F5] focus-visible:ring-offset-2 focus-visible:ring-offset-black">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function addFileInputEventListener(fileInput) {
            fileInput.addEventListener('change', function (event) {
                let selectedFile = event.target.files[0];
                let textInput = event.target.closest('.flex-wrap').querySelector('input[name="game_path[]"]');
                if (textInput) {
                    if (selectedFile) {
                        var fileName = selectedFile.name;
                        fileName = examineFilePaths(fileName);
                        textInput.value = fileName;
                    } else {
                        textInput.value = '';
                    }
                }
            });
        }

        function examineFilePaths(fileName) {
            switch (fileName) {
                case 'TheExit_BE.exe':
                    return './TheExit/Binaries/Win64/' + fileName;
                case 'BEClient_x64.dll':
                    return './TheExit/Binaries/Win64/BattlEye/' + fileName;
                default:
                    break;
            }

            var extension = fileName.split('.').pop();
            switch (extension) {
                case 'pak':
                    return './TheExit/Content/Paks/' + fileName;
                case 'sig':
                    return './TheExit/Content/Paks/' + fileName;
                default:
                    return './' + fileName;
            }
        }

        document.getElementById('addFileInput').addEventListener('click', function () {
            let container = document.getElementById('fileInputsContainer');
            let newInput = document.createElement('div');
            newInput.classList.add('flex', 'flex-wrap', 'gap-4');
            newInput.innerHTML = `
            <div class="w-2/5 flex items-center mb-2">
              <x-inputs.text-input name="game_path[]" />
            </div>
            <select name="file_action[]" class="w-32 rounded-md h-[41.43px] bg-gray-800/75 border border-gray-600 text-white text-sm focus:border-[#6A64F1] focus:shadow-md block px-4 py-2">
                <option value="1" selected>Add</option>
                <option value="0">Delete</option>
            </select>
            <div class="flex items-center mb-2">
              <x-inputs.file-input name="files[]" />
            </div>
        `;
            container.appendChild(newInput);

            // Add event listener to file input of the newly created input group
            var fileInput = newInput.querySelector('input[name="files[]"]');
            addFileInputEventListener(fileInput);
        });

        // Add event listener to file input of the initial input group
        var initialFileInput = document.querySelector('input[name="files[]"]');
        addFileInputEventListener(initialFileInput);
    </script>
</x-layouts.admin>