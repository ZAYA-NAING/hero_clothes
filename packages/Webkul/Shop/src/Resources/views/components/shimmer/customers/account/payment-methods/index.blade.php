@props(['count' => 1])

<div class="mb-7">
    <div class="hidden flex items-center justify-between">
        <h2 class="shimmer h-8 w-[225px]"></h2>
        <span class="shimmer h-6 w-6"></span>
    </div>

    <div class="mt-8 flex flex-wrap gap-7">
        @foreach (range(1, $count) as $i)
            <div class="relative max-sm:max-w-full max-sm:flex-auto">
                    <span class="shimmer absolute top-5 block h-6 w-6 rounded-full ltr:right-5 rtl:left-5"></span>

                <label class="block w-[190px] rounded-xl border border-zinc-200 p-5 max-sm:w-full">
                        <div class="shimmer h-[45px] w-[45px]"></div>

                        <p class="shimmer mt-1.5 h-[21px] w-full"></p>
                </label>
            </div>
        @endforeach
    </div>
    {{-- <div class="mt-8 flex flex-wrap">
        @foreach (range(1, 1) as $i)
            <div class="relative m-auto grid w-full items-center justify-items-center py-32 text-center">
                    <div class="shimmer h-[100px] w-[100px]"></div>
                    <div class="shimmer mt-2.5 h-[21px] w-full"></div>
            </div>
        @endforeach
    </div> --}}
</div>
