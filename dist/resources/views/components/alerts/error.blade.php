@props(['heading'])
<div id="alert" class="w-9/12 mx-auto mt-2 bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert">
    <div class="flex">
        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 14H9v-2h2v2zm0-4H9V5h2v5z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            @if (!empty($heading))
            <p class="font-bold">{{ $heading }}</p>
            @endif
            <p class="text-sm">{{ $slot }}</p>
        </div>
        <div class="ml-auto">
            <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.style.display='none';">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M15.293 5.293a1 1 0 0 1 1.414 1.414L11.414 10l5.293 5.293a1 1 0 1 1-1.414 1.414L10 11.414l-5.293 5.293a1 1 0 1 1-1.414-1.414L8.586 10 3.293 4.707a1 1 0 0 1 1.414-1.414L10 8.586l5.293-5.293a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.getElementById('closeAlert').addEventListener('click', function () {
        document.getElementById('alert').style.display = 'none';
    });
</script>

