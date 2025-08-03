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
            <div class="flex items-center my-4 w-1/2">
                <div class="flex-auto">
                    <label for="patchlines" class="mr-4 font-medium text-gray-900 dark:text-white">
                        Select a patchline:
                    </label>
                    <x-inputs.dropdown 
												id="patchlines"
												name="patchline"
												:cases="Patchline::cases()"
												:selected="$patchline"
                        onchange="this.form.submit()"
												required />
                </div>
								
                <div class="flex flex-auto">
                    <label for="additional_files">
                        Show additional mods:
                    </label>
                    <x-inputs.checkbox
												class="w-6 h-6 ml-4"
												id="additional_files"
												name="additional_files"
                        :checked="$showAdditionalFiles"
												value="1"
												onchange="this.form.submit()" />
                </div>
            </div>
        </form>

        <div class="flex flex-col items-center justify-center">
            <table>
                <thead>
                    <th class="w-12"></th>
                    @if ($showAdditionalFiles)
                        <th>Name</th>
                        <th>Description</th>
                        <th>Filename / Hash</th>
                    @else
                        <th>Filename</th>
                        <th>Hash</th>
                    @endif
                    <th class="w-24">Size</th>
                    <th class="w-32">Last update</th>
                    <th class="w-36">Actions</th>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                        <tr @class([
                            'text-center',
                            '!bg-green-600 hover:!bg-green-500' => $file->action === FileAction::ADD,
                            '!bg-red-600 hover:!bg-red-500' => $file->action === FileAction::DELETE,
                        ])>
                            <td>
                                @if ($file->children && $file->children->count() > 0)
                                    <button class="expand-row" onclick="toggleRow({{ $file->id }})">
                                        <x-icons.plus id="icon-plus-{{ $file->id }}" />
                                        <x-icons.minus id="icon-minus-{{ $file->id }}" class="hidden" />
                                    </button>
                                @endif
                            </td>
                            @if ($showAdditionalFiles)
                                <td>
                                    {{ $file->name }}
                                </td>
                                <td>
                                    {{ $file->description }}
                                </td>
                                <td title="{{ $file->game_path }}">
                                    {{ $file->filename }} <br>
                                    {{ $file->hash }}
                                </td>
                            @else
                                <td title="{{ $file->game_path }}">
                                    {{ $file->filename }}
                                    {{ $file->parent }}
                                </td>
                                <td>
                                    {{ $file->hash }}
                                </td>
                            @endif
                            <td>
                                @if ($file->fileExists())
                                    {{ round($file->getFileSize() / 1000, 1) }} kB
                                @else
                                    N/A
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
                                    <button class="">Mark for
                                        {{ $file->action->value ? 'delete' : 'add' }}</button>
                                </form>
                                <form
                                    action="{{ route('file-manager.destroy', ['file_manager' => $file->getTopParent()->id]) }}"
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

                        @if ($file->children && $file->children->count() > 0)
                            <tr class="expandable-content hidden hover:bg-opacity-60" id="content-{{ $file->id }}">
                                <td></td>
                                <td class="p-0" colspan="{{ $showAdditionalFiles ? '7' : '5' }}">
                                    <table class="w-full text-center">
                                        <thead>
                                            <tr class="pointer-events-none">
                                                @if ($showAdditionalFiles)
                                                    <th class="p-2 border-b-2 border-inherit w-36">File uploaded</th>
                                                    <th class="p-2 border-b-2 border-inherit">Description</th>
                                                @else
                                                    <th class="p-2 border-b-2 border-inherit w-64">File uploaded</th>
                                                @endif
                                                <th class="p-2 border-b-2 border-inherit w-72">Hash</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($file->children as $child)
                                                <tr @class([
                                                    'text-center',
                                                    '!bg-red-600 hover:!bg-red-500' => $child->action === FileAction::ADD,
                                                    '' => $child->action === FileAction::DELETE,
                                                ])>
                                                    <td class="p-2">
                                                        {{ $child->created_at }}
                                                    </td>
                                                    @if ($showAdditionalFiles)
                                                        <td class="p-2">
                                                            {{ $child->description }}
                                                        </td>
                                                    @endif
                                                    <td class="p-2">{{ $child->hash }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="mx-auto w-full max-w-screen-2xl mt-8">
                <form action="{{ route('file-manager.store') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="patchline" value="{{ request()->input('patchline') ?? '0' }}">
                    <input type="hidden" name="is_additional"
                        value="{{ request()->input('additional_files') ?? '0' }}">
                    @csrf
                    <div id="fileInputsContainer">
                        @if ($showAdditionalFiles)
                            <div class="flex flex-wrap gap-4">
                                <div class="w-2/12 flex items-center mb-2">
                                    <x-inputs.text-input name="game_mod_name[]" placeholder="Mod name" />
                                </div>
                                <div class="w-5/12 flex items-center mb-2">
                                    <x-inputs.text-input name="game_mod_description[]" placeholder="Mod description" />
                                </div>
                            </div>
                        @endif
                        <div class="flex flex-wrap gap-4">
                            <div class="w-6/12 flex items-center mb-2">
                                <x-inputs.text-input name="game_path[]" />
                            </div>
                            <select name="file_action[]"
                                class="w-1/12 rounded-md h-[41.43px] bg-gray-800/75 border border-gray-600 text-white text-sm focus:border-[#6A64F1] focus:shadow-md block px-4 py-2">
                                <option value="1" selected>Add</option>
                                <option value="0">Delete</option>
                            </select>
                            <div class="flex items-center mb-2">
                                <x-inputs.file-input name="files[]" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        @if (!$showAdditionalFiles)
                            <div class="w-auto mx-2">
                                <x-inputs.button type="button" id="addFileInput">
                                    Add More Files
                                </x-inputs.button>
                            </div>
                        @endif
                        <div class="w-auto mx-2">
                            <x-inputs.button>
                                Submit
                            </x-inputs.button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function toggleRow(id) {
            const content = document.getElementById('content-' + id);
            const iconPlus = document.getElementById('icon-plus-' + id);
            const iconMinus = document.getElementById('icon-minus-' + id);

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                iconMinus.classList.remove('hidden');
                iconPlus.classList.add('hidden');
            } else {
                content.classList.add('hidden');
                iconMinus.classList.add('hidden');
                iconPlus.classList.remove('hidden');
            }
        }

        function addFileInputEventListener(fileInput) {
            fileInput.addEventListener('change', function(event) {
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

        @if (!$showAdditionalFiles)
            document.getElementById('addFileInput').addEventListener('click', function() {
                let container = document.getElementById('fileInputsContainer');
                let newInput = document.createElement('div');
                newInput.classList.add('flex', 'flex-wrap', 'gap-4');
                newInput.innerHTML = `
                    <div class="w-6/12 flex items-center mb-2">
                    <x-inputs.text-input name="game_path[]" />
                    </div>
                    <select name="file_action[]" class="w-1/12 rounded-md h-[41.43px] bg-gray-800/75 border border-gray-600 text-white text-sm focus:border-[#6A64F1] focus:shadow-md block px-4 py-2">
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

                window.scrollTo(0, document.body.scrollHeight);
            });
        @endif

        // Add event listener to file input of the initial input group
        var initialFileInput = document.querySelector('input[name="files[]"]');
        addFileInputEventListener(initialFileInput);
    </script>
</x-layouts.admin>
