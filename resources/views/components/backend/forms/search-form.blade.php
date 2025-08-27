<div class="grow {{ $class }}">
    <form action="" method="GET" class="flex flex-col md:items-center md:flex-row items-stretch gap-2">
        {{ $slot }}
        <x-backend.inputs.search-input name="search" placeholder="{!! translate($placeholder) !!}" value="{!! $searchKey !!}"
            class="" />
    </form>
</div>
