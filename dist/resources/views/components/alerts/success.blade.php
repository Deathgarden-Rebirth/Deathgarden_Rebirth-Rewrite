@props(['heading'])
<div id="alert" class="w-9/12 mx-auto mt-2 bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
  <div class="flex">
    <div class="py-1">
      <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
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
          <path fill-rule="evenodd" d="M15.293 5.293a1 1 0 0 1 1.414 1.414L11.414 10l5.293 5.293a1 1 0 1 1-1.414 1.414L10 11.414l-5.293 5.293a1 1 0 1 1-1.414-1.414L8.586 10 3.293 4.707a1 1 0 0 1 1.414-1.414L10 8.586l5.293-5.293a1 1 0 0 1 1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</div>

<script>
  document.getElementById('closeAlert').addEventListener('click', function() {
    document.getElementById('alert').style.display = 'none';
  });
</script>