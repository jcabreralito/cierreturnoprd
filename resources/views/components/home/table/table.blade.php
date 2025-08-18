@props(['headers' => [], 'tblClass' => 'tblCapacitaciones tblHrsExtra'])

<div class="relative flex flex-col w-full h-full text-gray-700 overflow-x-auto bg-white shadow-md rounded-xl bg-clip-border custom-scrollbar max-h-[52vh]">
    <div class="overflow-y-auto custom-scrollbar">
        <table class="w-full text-left table-auto min-w-max text-xxs {{ $tblClass }}">
            <thead class="bg-white sticky top-0 z-10">
                <tr>
                    @php
                        // Eliminamos los elementos nulos del array
                        $headers = array_filter($headers);
                    @endphp

                    @foreach ($headers as $header)
                        <x-home.table.th :isSortable="$header[1]" :classList="$header[2]" :tblname="$header[3]">
                            {{ $header[0] }}
                        </x-home.table.th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
