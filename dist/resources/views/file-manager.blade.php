<x-raw-layout>
  <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie -->
  <h1 class="text-4xl font-semilight p-10">Deathgarden file manager</h1>
  @if(Session::has('alert-error'))
    <x-alerts.error heading="An error occured">{!! Session::get('alert-error') !!}</x-alerts.error>
  @endif
  @if(Session::has('alert-success'))
    <x-alerts.success heading="Success">{!! Session::get('alert-success') !!}</x-alerts.success>
  @endif
  @if(Session::has('alert-warning'))
    <x-alerts.warning heading="Warning">{!! Session::get('alert-warning') !!}</x-alerts.warning>
  @endif
  <div class="flex flex-col items-center justify-center p-12">
    <div class="mx-auto w-full max-w-7xl">
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
          <tr class="text-center">
            <x-tables.td>
              {{ $file->name }}
            </x-tables.td>
            <x-tables.td>
              {{ $file->hash }}
            </x-tables.td>
            <x-tables.td>
              @if(Storage::disk('dg_public')->exists($file->name))
                  {{ Storage::disk('dg_public')->size($file->name) / 1000 }} kB
              @else
                  Error while fetching file
              @endif
            </x-tables.td>
            <x-tables.td>
              {{ $file->updated_at }}
            </x-tables.td>
            <x-tables.td>
              <!-- <a href="">Delete</a> -->
            </x-tables.td>
          </tr>
          @endforeach
        </tbody>
      </x-tables.table>
    </div>

    <div class="mx-auto w-full max-w-7xl mt-8">
      <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="fileInputsContainer">
          <div class="flex flex-wrap gap-4">
            <div class="w-3/5 flex items-center mb-2">
              <x-inputs.text-input name="game_path[]" />
            </div>
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
            <div class="w-3/5 flex items-center mb-2">
              <x-inputs.text-input name="game_path[]" />
            </div>
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
