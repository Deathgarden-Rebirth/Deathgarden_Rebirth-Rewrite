<x-raw-layout>
  <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie -->  
  <h1 class="text-4xl font-semilight p-10">Deathgarden file manager</h1>

  <form action="{{ url()->current() }}" method="GET">
    <div class="flex flex-row items-center">
      <label for="patchlines" class="block p-4 font-medium text-gray-900 dark:text-white">Select a patchline:</label>
      <select id="patchlines" name="patchline" class="w-32 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onchange="this.form.submit()">
            <option value="0" {{ request()->input('patchline') == '0' ? 'selected' : '' }}>Live</option>
            <option value="1" {{ request()->input('patchline') == '1' ? 'selected' : '' }}>Dev</option>
        </select>
      </div>
  </form>

  @if(Session::has('alert-error'))
  <x-alerts.error heading="An error occured">{!! Session::get('alert-error') !!}</x-alerts.error>
  @endif
  @if(Session::has('alert-success'))
  <x-alerts.success heading="Success">{!! Session::get('alert-success') !!}</x-alerts.success>
  @endif
  @if(Session::has('alert-warning'))
  <x-alerts.warning heading="Warning">{!! Session::get('alert-warning') !!}</x-alerts.warning>
  @endif

  </div>
  <div class="flex flex-col items-center justify-center p-12">
    <div class="mx-auto w-full max-w-screen-2xl">
      <x-tables.table>
        <x-tables.thead>
          <x-tables.th>Name</x-tables.th>
          <x-tables.th>Hash</x-tables.th>
          <x-tables.th>Size</x-tables.th>
          <x-tables.th>Last update</x-tables.th>
          <x-tables.th>Actions</x-tables.th>
        </x-tables.thead>
        <tbody>
          @foreach($files as $file)
          <tr class="text-center {{ $file->action->value ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500' }}">
            <x-tables.td title="{{$file->game_path}}">
              {{ $file->name }}
            </x-tables.td>
            <x-tables.td>
              {{ $file->hash }}
            </x-tables.td>
            <x-tables.td>
              @if(Storage::disk('patches')->exists(str($file->patchline->name)->lower().'/'.$file->name))
              {{ Storage::disk('patches')->size(str($file->patchline->name).'/'.$file->name) / 1000 }} kB
              @else
              Error while fetching file
              @endif
            </x-tables.td>
            <x-tables.td>
              {{ $file->updated_at }}
            </x-tables.td>
            <x-tables.td>
              <form action="{{ route('file-manager.update', ['file_manager' => $file->id]) }}" method="POST">
                @method('PUT')
                @csrf
                <button class="">Mark for {{ $file->action->value ? 'delete' : 'add' }}</button>
              </form>
              <form action="{{ route('file-manager.destroy', ['file_manager' => $file->id]) }}" method="POST">
                @method('DELETE')
                @csrf
                <button class="" onclick="return confirm('This will delete the file on the server, but won\'t do any actions on the clients.\nAre you sure?')">Delete</button>
              </form>
            </x-tables.td>
          </tr>
          @endforeach
        </tbody>
      </x-tables.table>
    </div>

    <div class="mx-auto w-full max-w-screen-2xl mt-8">
      <form action="{{ route('file-manager.store') }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="patchline" value="{{ request()->input('patchline') ?? '0' }}">
        @csrf
        <div id="fileInputsContainer">
          <div class="flex flex-wrap gap-4">
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
          </div>
        </div>

        <div class="flex justify-end mt-4">
          <div class="w-auto mx-2">
            <button type="button" id="addFileInput" class="inline-flex rounded-md bg-gray-800/75 px-6 py-2 font-semibold text-gray-300 hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#BD92F5] focus-visible:ring-offset-2 focus-visible:ring-offset-black">
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

  <script>
    function addFileInputEventListener(fileInput) {
      fileInput.addEventListener('change', function(event) {
        var selectedFile = event.target.files[0];
        var textInput = event.target.closest('.flex-wrap').querySelector('input[name="game_path[]"]');
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

    document.getElementById('addFileInput').addEventListener('click', function() {
      var container = document.getElementById('fileInputsContainer');
      var newInput = document.createElement('div');
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
</x-raw-layout>